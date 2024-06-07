<?php

namespace App\Controllers;

use App\Utils\Util;
use App\Core\Config;
use App\Core\Controller;
use App\Models\BookModel;
use App\Services\BooksService;

// Kontroler akcji dla widoków książek.
class BooksController extends Controller
{
    private const ADD_OP_PERFORMED = 'book_add_op_performed'; // wyzwalacz przesłania formularza dodającego nową książkę
    private $_service; // instancja serwisu

    public function __construct()
    {
        parent::__construct(); // wywołenie konstruktora klasy bazowej (stworzenie instancji klasy renderer)
        $this->_service = BooksService::get_instance(); // pobranie obiektu typu singleton z serwisu
    }

    // Wyświetlanie wszystkich książek w formie tabeli. Dostęp dla obu poziomów uprawnień, z różnicami w interfejsie.
    // Alias dla endpointu index.php?action=books, równoznaczny z endpointem index.php?action=books/show.
	public function show()
    {
        Util::redirect_when_not_logged(); // jeśli jakikolwiek użytkownik nie jest zalogowany, przekieruj do logowania
        $result = $this->_pdo->get_data_from_db("SELECT * FROM books", BookModel::class); // pobierz wszystkie książki z systemu
        $this->renderer->render('show_books', array(
            'books_data' => $result,
            'header_mode' => $_SESSION['logged_user']['user_role'] === Config::get('__ADMIN_ROLE') ? 'admin' : 'user',
        ));
    }

    // Dodawanie nowej książki. Dostęp tylko dla administratora.
    // Alias dla endpointu index.php?action=books/add.
    public function add()
    {
        Util::redirect_when_not_logged('__ADMIN_ROLE'); // przekieruj do panelu logowania, jeśli nie jest zalogowany na konto admina
        if (isset($_POST[self::ADD_OP_PERFORMED])) // sprawdzenie, czy doszło do przesłania formularza
        {
            $this->_service->create_new_book(); // dodaj nową książkę przy pomocy metody serwisu
        }
        $this->renderer->render('add_book', array(
            'self_redirect' => Config::get('__SELF_SANITIZED') . '?action=books/add',
            'op_performed' => self::ADD_OP_PERFORMED,
            'form_data' => $this->_service->get_form_validatior_book(),
            'banner_text' => $this->_service->get_banner_text(),
            'banner_active_class' => !empty($this->_service->get_banner_text()) ? 'app__banner--enabled' : '',
        ));
    }

    // Wyświetlanie wszystkich książek wypożyczonych przez zalogowanego czytelnika, z możliwością zwrotu. Dostęp tylko z dla zalogowanych.
    // Alias dla endpointu index.php?action=books/rents.
    public function rents()
    {
        Util::redirect_when_not_logged(); // przekieruj do panelu logowania, jeśli nie jest zalogowany na konto
        $auth_values = $this->_service->check_authentication_values(); // zwaliduj zalogowanego użytkownika
        // przeszukaj i zwróć wszystkie wypożyczone książki użytkownika
        $rented_books = $this->_service->return_rents_books($auth_values['user']);
        $this->renderer->render('show_rented_books', array(
            'rented_books_data' => $rented_books,
            'is_self_user' => $auth_values['is_self_user'], // jeśli użytkownik edytuje samego siebie
            'user_full_name' => $auth_values['user_full_name'],
            'header_mode' => $_SESSION['logged_user']['user_role'] === Config::get('__ADMIN_ROLE') ? 'admin' : 'user',
            'banner_text' => $this->_service->get_banner_text(),
            'banner_active_class' => !empty($this->_service->get_banner_text()) ? 'app__banner--enabled' : '',
            'banner_mode_class' => $this->_service->get_banner_error() ? 'app__banner--error' : 'app__banner--info',
        ));
    }

    // Akcja do wypożyczenia konkretnej książki przez zalogowanego użytkownika.
    // Alias dla endpointu index.php?action=books/rent?_bookid=INT.
    public function rent()
    {
        Util::redirect_when_not_logged(); // jeśli jakikolwiek użytkownik nie jest zalogowany, przekieruj do logowania
        // sparametryzuj uniwersalną metodę na wypożyczenie książki
        $this->_service->rent_refund_selected_book( 
            'wypożyczenie', 'wypożyczona',
            "INSERT INTO books_users_binding (`user_id`, `book_id`) VALUES (?,?)",
            "UPDATE books SET copies = copies - 1 WHERE id = ?",
            true,
        );
        $result = $this->_pdo->get_data_from_db("SELECT * FROM books", BookModel::class);
        $this->renderer->render('show_books', array(
            'books_data' => $result,
            'banner_text' => $this->_service->get_banner_text(),
            'banner_active_class' => !empty($this->_service->get_banner_text()) ? 'app__banner--enabled' : '',
            'header_mode' => $_SESSION['logged_user']['user_role'] === Config::get('__ADMIN_ROLE') ? 'admin' : 'user',
            'banner_mode_class' => $this->_service->get_banner_error() ? 'app__banner--error' : 'app__banner--info',
        ));
    }

    // Akcja do zwrócenia wypożyczonej konkretnej książki przez zalogowanego użytkownika.
    // Alias dla endpointu index.php?action=books/refund?_bookid=INT.
    public function refund()
    {
        Util::redirect_when_not_logged(); // jeśli jakikolwiek użytkownik nie jest zalogowany, przekieruj do logowania
        $auth_values = $this->_service->check_authentication_values(true); // zwaliduj zalogowanego użytkownika
        // sparametryzuj uniwersalną metodę na zwrócenie książki
        $this->_service->rent_refund_selected_book(
            'zwrócenie', 'zwrócona',
            "DELETE FROM books_users_binding WHERE `user_id` = ? AND `book_id` = ? LIMIT 1",
            "UPDATE books SET copies = copies + 1 WHERE id = ?",
        );
        // przeszukaj i zwróć wszystkie wypożyczone książki użytkownika
        $rented_books = $this->_service->return_rents_books($auth_values['user']);
        $this->renderer->render('show_rented_books', array(
            'rented_books_data' => $rented_books,
            'is_self_user' => $auth_values['is_self_user'], // jeśli użytkownik edytuje samego siebie
            'user_full_name' => $auth_values['user_full_name'],
            'header_mode' => $_SESSION['logged_user']['user_role'] === Config::get('__ADMIN_ROLE') ? 'admin' : 'user',
            'banner_text' => $this->_service->get_banner_text(),
            'banner_active_class' => !empty($this->_service->get_banner_text()) ? 'app__banner--enabled' : '',
            'banner_mode_class' => $this->_service->get_banner_error() ? 'app__banner--error' : 'app__banner--info',
        ));
    }

    // Wyświetlanie wszystkich książek w systemie. Dostęp dla obu poziomów uprawnień, z różnicami w interfejsie.
    // Alias dla endpointu index.php?action=books/show, równoznaczne z index.php?action=books.
    public function index()
    {
        header('Location:index.php?action=books/show'); // przekierowanie na adres
        ob_end_flush(); // zwolnienie bufora
    }
}
