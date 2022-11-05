<?php

// Główna klasa przechowująca obiekt PDO i podstawowe metody operujące na bazie danych.
class DbContext
{
    private static $_instance;  // instancja klasy singleton
    private $_db_handler; // instancja PDO

    //--------------------------------------------------------------------------------------------------------------------------------------

    private function __construct()
    {
        // stworzenie instancji klasy PDO i połączenie się z bazą danych
        $this->_db_handler = new PDO(Config::get('__DB_DSN'), Config::get('__DB_USERNAME'), Config::get('__DB_PASSWORD'), 
                                     Config::get('__DB_INIT_COMMANDS'));
        $this->_db_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // ustawienie trybu błędów na rzucanie wyjątów
    }

    //--------------------------------------------------------------------------------------------------------------------------------------    

    // stworzenie (jeśli obiekt nie istnieje) oraz zwrócenie instancji PDO
    public static function get_instance()
    {
        if (self::$_instance == null)
        {
            self::$_instance = new DbContext();
        }
        return self::$_instance;
    }
}