<?php

namespace App\Core;

// Klasa abstrakcyjna serwisu, implementowana w serwisach aplikacji, przechowująca referencje do obiektów PDO
abstract class Service
{
    protected $_banner_text = ''; // globalna wiadomość wyświetlana w banerze
    protected $_banner_error = false; // flaga decydująca, czy wiadomość w banerze jest błędem

    protected $_pdo; // instancja klasy PDO
    protected $_dbh; // handler do bazy danych (PDO)

    //--------------------------------------------------------------------------------------------------------------------------------------

    protected function __construct()
    {
        $this->_pdo = DbContext::get_instance();
        $this->_dbh = $this->_pdo->get_handler();
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda zwracająca wiadomość banera w widoku
    public function get_banner_text()
    {
        return $this->_banner_text;
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda zwracająca true, jeśli wyświetlany baner ma być banerem błędu
    public function get_banner_error()
    {
        return $this->_banner_error;
    }
}
