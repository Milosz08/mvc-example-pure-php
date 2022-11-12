<?php

namespace App\Core;

// Klasa przechowująca metody odpowiadające za ładowanie rdzenia aplikacji (plików "core").
class CoreLoader
{
    private static $_instance; // instancja loadera

    //--------------------------------------------------------------------------------------------------------------------------------------

    private function __construct()
    {
        $this->scan_dirs_and_load();
    }
    
    //--------------------------------------------------------------------------------------------------------------------------------------

    // Skanowanie wybranych katalogów i ładowanie plików do kontekstu aplikacji.
    private function scan_dirs_and_load()
    {
        $root_dir = realpath(dirname(__FILE__) . '/..'); // katalog główny (przejście stopień wyżej, /src/)
        foreach (__SCANNER_DIRS__ as $path) // pętla przechodząca przez wszystkie katalogi
        {
            // wszystkie pliki w wybranym katalogu z rozszerzeniem php
            $files_array = glob($root_dir . __SEP__ . $path . __SEP__ . "*.php", GLOB_BRACE);
            foreach ($files_array as $file) // przejście przez wszystkie pliki katalogu
            {
                if ($file !== __FILE__) require_once $file; // jeśli plik nie odnosi się do klasy CoreLoader, załaduj
            }
        }
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda uruchamiana przez uruchomieniem głównej metody run z klasy MvcApplication umożliwiająca stworzenie instancji Loadera i w
    // wyniku tego załadowanie do kontekstu aplikacji plików.
    public static function load()
    {
        if (self::$_instance == null) self::$_instance = new CoreLoader();
    }
}
