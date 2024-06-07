<?php

namespace App\Core;

// Klasa przechowująca metody odpowiadające za renderowanie widoków.
class Renderer
{
    private static $_instance; // instancja obiektu typu singleton

    private function __construct() {}

    // Metoda odpowiadająca za renderowanie widoku (na podstawie nazwy przekazanej w parametrze).
    // Jeśli nie znajdzie widoku, error. Pierwszy parametr odpowiada za nazwę widoku (bez rozszerzenia)
    // a drugi za opcjonalne parametry (model) przekazywane do widoku.
    public function render($view, $data = array())
    {
        require_once Config::get('__MVC_VIEWS_DIR') . $view . '.php';
    }

    // stworzenie (jeśli obiekt nie istnieje) oraz zwrócenie instancji klasy Renderer
    public static function get_instance()
    {
        if (self::$_instance == null) self::$_instance = new Renderer();
        return self::$_instance;
    }
}
