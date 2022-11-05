<?php

namespace App\Utils;

// Klasa przechowująca statyczne metody pomocnicze do walidacji formularzy.
class Util
{
    // Metoda odpowiadająca za sprawdzenie, czy pole formularza nie jest puste (na podstawie klucza w tablicy $_POST).
    public static function check_if_input_not_empty($input_name, $descript_name)
    {
        $error_message = '';
        $sanitized_value = htmlspecialchars($_POST[$input_name]); // sanetyzacja pola przeciwko atakom XSS
        if (empty($sanitized_value)) // jeśli wartość jest pusta, error
        {
            $error_message = '* Wartość' . $descript_name . ' nie może być pusta.';
        }
        // zwróć tablicę asocjacyjną z informacjami po weryfikacji pola formularza
        return array(
            'value' => $sanitized_value,
            'error_message' => $error_message
        );
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda odpowiadająca za sprawdzenie, czy został przesłany dodakowy parametr id używany do edycji rekordów, jeśli nie przekierowanie
    // Przekierowanie również, gdy parametr nie jest liczbą.
    public static function check_if_send_element_id($param_name, $redirect_controller, $redirect_action)
    {
        if (!isset($_GET['_' . $param_name . 'id']) || !is_numeric($_GET['_' . $param_name . 'id']))
        {
            header('Location:index.php?action=' . $redirect_controller . '/' . $redirect_action);
        }
    }
}