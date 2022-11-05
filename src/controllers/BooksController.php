<?php

namespace App\Controllers;

use App\Utils\Util;
use App\Core\Config;
use App\Core\Controller;
use App\Services\BooksService;

// Kontroler akcji dla widoków książek.
class BooksController extends Controller
{
    private const ADD_OP_PERFORMED = 'book_add_op_performed'; // wyzwalacz przesłania formularza dodającego nową książkę
    private const EDIT_OP_PERFORMED = 'book_edit_op_performed'; // wyzwalacz przesłania formularza edytującego wybraną książkę
    private $_service; // instancja serwisu

    //--------------------------------------------------------------------------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();
        $this->_service = new BooksService();
    }

    //--------------------------------------------------------------------------------------------------------------------------------------
	
    // Wyświetlanie wszystkich książek w formie tabeli. Dostęp dla obu poziomów uprawnień, z różnicami w interfejsie.
    // Alias dla endpointu index.php?action=books, równoznaczny z endpointem index.php?action=books/show.
	public function index()
    {
        $this->renderer->render('show_books', array(

        ));
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Dodawanie nowej książki. Dostęp tylko dla administratora
    // Alias dla endpointu index.php?action=books/add.
    public function add()
    {
        if (isset($_POST[self::ADD_OP_PERFORMED])) // sprawdzenie, czy doszło do przesłania formularza
        {




        }
        $this->renderer->render('add_edit_book', array(
            'self_redirect' => Config::get('__SELF_SANITIZED') . '?action=books/add',
            'book_operation_name' => 'Dodaj',
            'op_performed' => self::ADD_OP_PERFORMED,
            'form_data' => $this->_service->get_form_validatior_book(),
        ));
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Edytowanie istniejącej książki. Dostęp tylko dla administratora
    // Alias dla endpointu index.php?action=books/edit?_bookid=INT.
    public function edit()
    {
        Util::check_if_send_element_id('book', 'books', 'show'); // sprawdź, czy przesłano parametr z id
        if (isset($_POST[self::EDIT_OP_PERFORMED])) // sprawdzenie, czy doszło do przesłania formularza
        {
            

        }
        $this->renderer->render('add_edit_book', array(
            'self_redirect' => Config::get('__SELF_SANITIZED') . '?action=books/edit&_bookid=' . $_GET['_bookid'],
            'book_operation_name' => 'Edytuj',
            'op_performed' => self::EDIT_OP_PERFORMED,
            'form_data' => $this->_service->get_form_validatior_book(),
        ));
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Wyświetlanie wszystkich książek wyporzyczonych przez konkretnego użytkownika. Dostęp tylko dla administratora.
    // Alias dla endpointu index.php?action=books/rented?_userid=INT.
    public function rented()
    {
        Util::check_if_send_element_id('user', 'users', 'show'); // sprawdź, czy przesłano parametr z id, jeśli nie przekieruj

        $this->renderer->render('show_users_rented_books', array(

        ));
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Wyświetlanie wszystkich książek wypożyczonych przez zalogowanego czytelnika, z możliwością zwrotu. Dostęp tylko z poziomu czytelnika.
    // Alias dla endpointu index.php?action=books/my_rented.
    public function my_rented()
    {
        $this->renderer->render('show_my_rented_books', array(

        ));
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Usuwanie wybranej książki na podstawie id. Książka nie może być przez nikogo wypożyczana. Dostęp tylko z poziomu administratora.
    // Alias dla endpointu index.php?action=books/remove&_bookid=INT.
    public function remove()
    {
        Util::check_if_send_element_id('book', 'books', 'show'); // sprawdź, czy przesłano parametr z id

        $this->renderer->render('show_books', array(

        ));
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Wyświetlanie wszystkich książek w systemie. Dostęp dla obu poziomów uprawnień, z różnicami w interfejsie.
    // Alias dla endpointu index.php?action=books/show, równoznaczne z index.php?action=books.
    public function show()
    {
        $this->index();
    }
}
