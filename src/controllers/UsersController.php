<?php

require_once './src/services/UsersService.php'; // import serwisu

class UsersController extends Controller
{
    private const ADD_OP_PERFORMED = 'user_add_op_performed'; // wyzwalacz przesłania formularza dodającego użytkownika
    private const EDIT_OP_PERFORMED = 'user_edit_op_performed'; // wyzwalacz przesłania formularza edytującego wybranego użytkownika
    private $_service; // instancja serwisu

    //--------------------------------------------------------------------------------------------------------------------------------------
    
    public function __construct()
    {
        parent::__construct();
        $this->_service = new UsersService();
    }
	
    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda wyświetlająca wszystkich użytkowników aplikacji. Dostęp tylko dla administratora.
    // Alias dla endpointu index.php?action=users, równoznaczny z endpointem index.php?action=users/show
	public function index()
    {
        $this->renderer->render('show_users', array(

        ));
	}

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda usuwająca wybranego użytkownika z bazy danych. Dostęp tylko dla administratora.
    // Alias dla endpointu index.php?action=users/remove&_userid=INT.
    public function remove()
    {
        Util::check_if_send_element_id('user', 'users', 'show'); // sprawdź, czy przesłano parametr z id
        
        $this->renderer->render('show_users', array(

        ));
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda dodająca nowego użytkownika do bazy danych. Dostęp tylko dla administratora.
    // Alias dla endpointu index.php?action=users/add
    public function add()
    {
        if (isset($_POST[self::ADD_OP_PERFORMED])) // sprawdź, czy przesłano formularz, jeśli nie to przejdź do renderowania widoku
        { 





        }
        $this->renderer->render('add_edit_user', array(
            'self_redirect' => Config::get('__SELF_SANITIZED') . '?action=users/add',
            'user_operation_name' => 'Dodaj',
            'op_performed' => self::ADD_OP_PERFORMED,
            'permissions' => $this->_service->parse_users_permissions(),
            'form_data' => $this->_service->get_form_validatior_user(),
        ));
	}

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda umożliwiająca edycję wybranego użytkownika. Dostęp tylko dla administratora.
    // Alias dla endpointu index.php?action=users/edit&_userid=INT.
    public function edit()
    {
        Util::check_if_send_element_id('user', 'users', 'show'); // sprawdź, czy przesłano parametr z id
        if (!isset($_POST[self::EDIT_OP_PERFORMED])) // sprawdź, czy przesłano formularz, jeśli nie to przejdź do renderowania widoku
        {
            



        }
        $this->renderer->render('add_edit_user', array(
            'self_redirect' => Config::get('__SELF_SANITIZED') . '?action=users/edit&_userid=' . $_GET['_userid'],
            'user_operation_name' => 'Edytuj',
            'op_performed' => self::EDIT_OP_PERFORMED,
            'permissions' => $this->_service->parse_users_permissions(),
            'form_data' => $this->_service->get_form_validatior_user(),
        ));
    }

    //--------------------------------------------------------------------------------------------------------------------------------------
    
    // Metoda wyświetlająca wszystkich użytkowników aplikacji. Dostęp tylko dla administratora.
    // Alias dla endpointu index.php?action=users/show, równoznaczny z endpointem index.php?action=users
    public function show()
    {
        $this->index();
    }
}