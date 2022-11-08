<?php

namespace App\Controllers;

use App\Utils\Util;
use App\Core\Config;
use App\Core\DbContext;
use App\Core\Controller;
use App\Models\UserModel;
use App\Services\UsersService;

// Kontroler akcji dla widoków użytkowników.
class UsersController extends Controller
{
    private const ADD_OP_PERFORMED = 'user_add_op_performed'; // wyzwalacz przesłania formularza dodającego użytkownika
    private const EDIT_OP_PERFORMED = 'user_edit_op_performed'; // wyzwalacz przesłania formularza edytującego wybranego użytkownika
    
    private $_service; // instancja serwisu

    // zapytanie zwracające wszystkich użytkowników z bazy danych
    private const GET_USERS_QUERY = "
        SELECT users.id AS id, `first_name`, `last_name`, `login`, `age`, roles.name AS `role_name`, roles.id AS `role_id`
        FROM users
        INNER JOIN roles ON users.role_id = roles.id
    ";

    //--------------------------------------------------------------------------------------------------------------------------------------
    
    public function __construct()
    {
        parent::__construct(); // wywołenie konstruktora klasy bazowej (stworzenie instancji klasy renderer)
        $this->_service = UsersService::get_instance(); // pobranie obiektu typu singleton z serwisu
    }
	
    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda wyświetlająca wszystkich użytkowników aplikacji. Dostęp tylko dla administratora.
    // Alias dla endpointu index.php?action=users, równoznaczny z endpointem index.php?action=users/show
	public function show()
    {
        Util::redirect_when_not_logged('__ADMIN_ROLE'); // przekieruj do panelu logowania, jeśli nie jest zalogowany na konto admina
        $result = $this->_pdo->get_data_from_db(self::GET_USERS_QUERY, UserModel::class); // pobierz wszystkich dostępnych użytkowników
        $this->renderer->render('show_users', array(
            'users_data' => $result,
        ));
	}

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda usuwająca wybranego użytkownika z bazy danych. Dostęp tylko dla administratora.
    // Alias dla endpointu index.php?action=users/remove&_userid=INT.
    public function remove()
    {
        Util::redirect_when_not_logged('__ADMIN_ROLE'); // przekieruj do panelu logowania, jeśli nie jest zalogowany na konto admina
        $this->_service->remove_user($_GET['_userid']); // usuń użytkownika
        $result = $this->_pdo->get_data_from_db(self::GET_USERS_QUERY, UserModel::class); // pobierz wszystkich dostępnych użytkowników
        $this->renderer->render('show_users', array(
            'users_count' => is_array($result) ? count($result) : 0, 
            'users_data' => $result,
            'banner_text' => $this->_service->get_banner_text(),
            'banner_active_class' => !empty($this->_service->get_banner_text()) ? 'app__banner--enabled' : '',
            'banner_mode_class' => $this->_service->get_banner_error() ? 'app__banner--error' : 'app__banner--info',
        ));
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda dodająca nowego użytkownika do bazy danych. Dostęp tylko dla administratora.
    // Alias dla endpointu index.php?action=users/add
    public function add()
    {
        Util::redirect_when_not_logged('__ADMIN_ROLE'); // przekieruj do panelu logowania, jeśli nie jest zalogowany na konto admina
        if (isset($_POST[self::ADD_OP_PERFORMED])) // sprawdź, czy przesłano formularz, jeśli nie to przejdź do renderowania widoku
        {
            $this->_service->create_new_user(); // stwórz nowego użytkownika przy pomocy metody serwisu
        }
        $this->renderer->render('add_edit_user', array(
            'self_redirect' => Config::get('__SELF_SANITIZED') . '?action=users/add',
            'user_operation_name' => 'Dodaj',
            'op_performed' => self::ADD_OP_PERFORMED,
            'permissions' => Util::parse_users_permissions(),
            'form_data' => $this->_service->get_form_validatior_user(),
            'banner_text' => $this->_service->get_banner_text(),
            'banner_active_class' => !empty($this->_service->get_banner_text()) ? 'app__banner--enabled' : '',
        ));
	}

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda umożliwiająca edycję wybranego użytkownika. Dostęp tylko dla administratora.
    // Alias dla endpointu index.php?action=users/edit&_userid=INT.
    public function edit()
    {
        Util::redirect_when_not_logged('__ADMIN_ROLE'); // przekieruj do panelu logowania, jeśli nie jest zalogowany na konto admina
        // sprawdź, czy użytkownik istnieje, jeśli nie przejdź do ścieżki index.php?action=users/show
        $editable_user = $this->_pdo->check_if_exist(DbContext::GET_USER_QUERY, UserModel::class, 'user', 'users', 'show');
        $this->_service->add_user_values_from_query($editable_user); // przepisz wartości do formularza
        if (isset($_POST[self::EDIT_OP_PERFORMED])) // sprawdź, czy przesłano formularz, jeśli nie to przejdź do renderowania widoku
        {
            $this->_service->edit_existing_user($editable_user->get_id()); // edytuj użytkownika przy pomocy metody serwisu
        }
        $this->renderer->render('add_edit_user', array(
            'self_redirect' => Config::get('__SELF_SANITIZED') . '?action=users/edit&_userid=' . $_GET['_userid'],
            'user_operation_name' => 'Edytuj',
            'op_performed' => self::EDIT_OP_PERFORMED,
            'permissions' => Util::parse_users_permissions($editable_user->get_role_name()),
            'form_data' => $this->_service->get_form_validatior_user(),
            'banner_text' => $this->_service->get_banner_text(),
            'banner_active_class' => !empty($this->_service->get_banner_text()) ? 'app__banner--enabled' : '',
        ));
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda umożliwiające pokazanie książek wypożyczonych przez wybranego użytkownika. Dostęp tylko dla administratora.
    // Alias dla endpointu index.php?action=users/books&_userid=INT.
    public function books()
    {
        Util::redirect_when_not_logged('__ADMIN_ROLE'); // przekieruj do panelu logowania, jeśli nie jest zalogowany na konto admina
        // sprawdź, czy użytkownik istnieje, jeśli nie przejdź do ścieżki index.php?action=users/show
        $editable_user = $this->_pdo->check_if_exist(DbContext::GET_USER_QUERY, UserModel::class, 'user', 'users', 'show');
        // sprawdź, czy wybrany użytkownik to ten sam co zalogowany (tylko zalogowany użytkownik może oddawać swoje książki)
        $self_user = $editable_user->get_id() === $_SESSION['logged_user']['user_id'];
        $this->renderer->render('show_rented_books', array(
            'is_self_user' => $self_user,
            'user_full_name' => $editable_user->get_first_name() . ' ' . $editable_user->get_last_name(),
            'header_mode' => $_SESSION['logged_user']['user_role'] === Config::get('__ADMIN_ROLE') ? 'admin' : 'user',
        ));
    }

    //--------------------------------------------------------------------------------------------------------------------------------------
    
    // Metoda wyświetlająca wszystkich użytkowników aplikacji. Dostęp tylko dla administratora.
    // Alias dla endpointu index.php?action=users/show, równoznaczny z endpointem index.php?action=users
    public function index()
    {
        header('Location:index.php?action=users/show'); // przekierowanie na adres
        ob_end_flush(); // zwolnienie bufora
    }
}