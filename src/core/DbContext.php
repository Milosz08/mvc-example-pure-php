<?php

namespace App\Core;

use PDO;

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

    // Metoda umożliwiająca pobranie danych z wybranej tabeli i zmapowanie na obiekt wybranej klasy której nazwa przekazywana 
    // jest w parametrach metody. Jeśli poda się parametr id, wówczas zostanie pobrany jeden rekord na podstawie tego parametru.
    // Jeśli nie znajdzie żadnego rekordu, zwraca false.
    public function get_data_from_db($sql_query, $model_clazz, $id = null)
    {
        $cursor = null;
        $data_array = array();
        if (isset($id)) // jeśli przechwytywany jest jeden konkretny rekord
        {
            // zapytanie wykonywane poprzez prep_statement w celu uniknięcia podatności na ataki SQL Injections, bo id jest
            // przechwytywane z adresu URL
            $prep_statement = $this->_db_handler->prepare($sql_query); // przygotuj zapytanie SQL
            $prep_statement->execute(array('id_sant' => (int) $id)); // wykonanie zapytania SQL
            $cursor = $prep_statement; // przypisanie do kursora przygotowanego zapytania
        }
        else // jeśli przechwytywane są wszystkie rekordy
        {
            // zwykłe zapytanie, bez prep_statement bo wartości nie przychodzą od użytkownika
            $cursor = $this->_db_handler->query($sql_query);
        }
        while ($mapped_objects = $cursor->fetchObject($model_clazz)) // zmapowanie danych z bazy na konkretny obiekt (model)
        {
            array_push($data_array, $mapped_objects); // dodaj do tablicy zmapowane obiekty
        }
        $cursor->closeCursor(); // zwolnienie zasobów
        return $data_array;
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda sprawdzająca, czy element istnieje w bazie danych (przekazywany poprzez $_GET). Jeśli istnieje, zwróć element. W przeciwnym
    // wypadku przekieruj do wybranego kontrolera na wybraną akcję.
    public function check_if_exist($sql_query, $model_clazz, $param_name, $redirect_controller, $redirect_action)
    {
        $id = $_GET['_' . $param_name . 'id']; // pobierz parametr z tablicy $_POST
        if (isset($id) && is_numeric($id)) // jeśli parametr nie jest nullem i jest liczbą
        {
            $result = $this->get_data_from_db($sql_query, $model_clazz, $id); // wykonaj zapytanie i zwróć dane
            if ($result) return $result[0]; // zwróć pierwszą wartość
        }
        header('Location:index.php?action=' . $redirect_controller . '/' . $redirect_action); // przekierowanie
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda getter zwracająca uchwyt do bazy danych
    public function get_handler()
    {
        return $this->_db_handler;
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