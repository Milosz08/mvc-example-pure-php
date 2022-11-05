<?php

// Klasa konfiguracyjna umożliwiająca wprowadzenie zmienny KLUCZ -> WARTOŚĆ do tablicy globalnej.
class Config
{
    private static $_values = array(); // globalna tablica zmiennych do konfiguracji aplikacji

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda umożliwiająca wstawienie nowej wartości do globalnej tablicy konfiguracji w postacji KLUCZ -> WARTOŚĆ. Niedozwolone jest
    // dodawanie wielu wartości z tym samym kluczem.
    public static function set($key, $value)
    {
        if (array_key_exists($key, self::$_values))
        {
            throw new Exception('Unable to add duplicate keys.');
        }
        self::$_values[$key] = $value;
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda umożliwiająca pobranie konkretnej wartości na podstawie przesłanego klucza. Jeśli klucz nie istnieje, wyjątek.
    public static function get($key)
    {
        if (!array_key_exists($key, self::$_values))
        {
            throw new Exception('Unable to localised value base key: ' . $key);
        }
        return self::$_values[$key];
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda umożliwiająca stworzenie ścieżki z systemowymi separatorami katalogów (co 1 segment)
    public static function build_path(...$segments)
    {
        return join(DIRECTORY_SEPARATOR, $segments) . DIRECTORY_SEPARATOR;
    }
}