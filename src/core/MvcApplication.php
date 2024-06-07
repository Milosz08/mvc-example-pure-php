<?php

namespace App\Core;

use ReflectionException;

// Główna klasa aplikacji odpowiadająca za obsługę i renderowanie odpowiednich widoków na
// podstawie parametrów w ścieżce zapytania.
class MvcApplication
{
    private static $_instance; // instancja obiektu typu singleton dla klasy
    private $controller_instance; // instancja aktualnie załadowanego kontrolera
    private $renderer_instance; // instancja klasy renderującej widoki

    private function __construct()
    {
        $this->renderer_instance = Renderer::get_instance();
        $this->render_mvc();
    }

    // Metoda odpowiadająca za tworzenie i wywoływanie metody kontrolera na podstawie parametrów
    // ścieżki URL. Jeśli nie znajdzie kontrolera, rzucany jest wyjątek który po przechwyceniu
    // wyświetla widok 404 (nie znaleziono zasobu).
    private function render_mvc()
    {
        try
        {
            $action_params = $this->parse_url(); // przetworzona ścieżka w postaci tablicy wartości
            $controller_with_extension = $action_params['controller'] . '.php'; // nazwa kontrolera z rozszerzeniem
        
            // jeśli plik kontrolera nie istnieje, rzuć wyjątek
            if (!file_exists(Config::get('__MVC_CONTROLLERS_DIR') . $controller_with_extension))
            {
                throw new ReflectionException();
            }
            require_once Config::get('__MVC_CONTROLLERS_DIR') . $controller_with_extension; // import pliku kontrolera
            // nazwa kontrolera wraz z przestrzenią nazw
            $controller_class_name = Config::get('__MVC_CONTROLLERS_NAMESPACE') . $action_params['controller'];
            $this->controller_instance = new $controller_class_name; // stworzenie instancji
            
            // sprawdź, czy podana metoda kontrolera istnieje, jeśli nie rzuć wyjątek
            if (!method_exists($this->controller_instance, $action_params['method']))
            {
                throw new ReflectionException();
            }
            // wywołaj wybraną metodę kontrolera przy użyciu refleksji
            call_user_func([ $this->controller_instance, $action_params['method'] ]);
        }
        catch(ReflectionException $e)
        {
            $this->renderer_instance->render('_not-found'); // renderuj widok 404
            die; // zakończ działanie skryptu
        }
    }

    // Metoda odpowiadająca za parsowanie adresu URL z parametrami zapytania. Jeśli nie znajdzie parametrów,
    // zwracane są domyślne zdefiniowane jako stałe w powyższej klasie. Jeśli natomiast znajdzie, zwracana jest nazwa
    // kontrolera (bez rozszerzenia) wraz z metodą.
    private function parse_url()
    {
        $action_type = Config::get('__MVC_DEF_METHOD'); // domyślna metoda wywoływana na kontrolerze
        if (!isset($_GET['action'])) // jeśli nie przesłano zapytania z parametrem action, zwróć domyślny kontroler i metodę
        {
            return array(
                'controller' => $this->parse_controller_name(Config::get('__MVC_DEF_CONTROLLER')),
                'method' =>     $action_type,
            );
        }
        // oddziel ze ścieżki nazwę kontrolera i metody
        $separate_controller_and_method = explode('/', filter_var(rtrim($_GET['action']), FILTER_SANITIZE_URL));
        if (count($separate_controller_and_method) > 1) // jeśli podano kontroler i metodę, nadpisz zmienną z metodą
        {
            $action_type = $separate_controller_and_method[1];
        }
        // w przeciwnym wypadku zwróć wybrany kontroler i metodę (na podstawie ścieżki zapytania)
        return array(
            'controller' => $this->parse_controller_name($separate_controller_and_method[0]),
            'method' =>     $action_type,
        );
    }

    // Metoda odpowiadająca za parsowanie nazwy kontrolera. Na podstawie parametru ustawia pierwszy znak na
    // wielką literę i dodaje suffix, zdefiniowany jako stała w powyższej klasie.
    private function parse_controller_name($controller_raw_name)
    {
        return ucfirst($controller_raw_name) . Config::get('__MVC_CONTROLLER_SUFFIX');
    }

    // główna metoda uruchamiająca aplikację poprzez stworzenie instancji klasy MvcApplication
    public static function run()
    {
        if (self::$_instance == null) self::$_instance = new MvcApplication();
    }
}
