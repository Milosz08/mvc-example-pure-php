<?php

namespace App\Core;

// Klasa abstrakcyjna serwisu, implementowana w serwisach aplikacji, przechowująca referencje do obiektów PDO
abstract class Service
{
    protected $_pdo; // instancja klasy PDO
    protected $_dbh; // handler do bazy danych (PDO)

    //--------------------------------------------------------------------------------------------------------------------------------------

    protected function __construct()
    {
        $this->_pdo = DbContext::get_instance();
        $this->_dbh = $this->_pdo->get_handler();
    }
}