<?php

namespace App\Utils;

use App\Core\Config;

// Klasa przechowująca statyczne metody pomocnicze do aplikacji.
class Util
{
    // Metoda odpowiadająca za sprawdzenie, czy pole formularza nie jest puste (na podstawie klucza w tablicy $_POST).
    public static function check_if_input_not_empty($input_name)
    {
        $error_message = '';
        $sanitized_value = htmlspecialchars($_POST[$input_name]); // sanetyzacja pola przeciwko atakom XSS
        if (empty(trim($sanitized_value))) // jeśli wartość jest pusta (wcześniej usunięte białe znaki), error
        {
            $error_message = '* Pole nie może być puste.';
        }
        // zwróć tablicę asocjacyjną z informacjami po weryfikacji pola formularza
        return array(
            'value' => $sanitized_value,
            'error_message' => $error_message,
        );
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda umożliwiająca walidację pola na podstawie wzoru regex oraz to, czy pole nie jest puste.
    public static function validate_regex_field($input_name, $regex_pattern)
    {
        $array_input = array('value' => '', 'error_message' => '');
        $array_input = self::check_if_input_not_empty($input_name);
        if (empty($array_input['error_message'])) // jeśli wiadomość error jest pusta
        {
            $sanitized_value = trim(htmlspecialchars($_POST[$input_name])); // sanetyzacja pola przeciwko atakom XSS
            if (!preg_match($regex_pattern, $sanitized_value)) // sprawdź, czy string nie jest zgodny ze wzorcem, jeśli tak error
            {
                return array(
                    'value' => $sanitized_value,
                    'error_message' => '* Niepoprawna wartość wejściowa.',
                );
            }
        }
        return $array_input;
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda walidująca dla inputu liczbowego przyjmująca nazwę inputu oraz zakres od do którego ma walidować (zakres ten jest 
    // obustronnie domknięty).
    public static function validate_number_field($input_name, $from_incl, $to_incl)
    {
        $error_message = '';
        // sanetyzacja pola przeciwko atakom XSS oraz filtrowanie wartości (filtr sanetyzujący liczby)
        $sanitized_value = filter_var($_POST[$input_name], FILTER_SANITIZE_NUMBER_INT);
        if (empty($_POST[$input_name])) // jeśli wartość jest pusta, error
        {
            $error_message = '* Pole nie może być puste.';
        }
        else
        {
            if (is_numeric($sanitized_value)) // jeśli wartość jest liczbą, sprawdź czy mieści się w przedziale
            {
                $parsed_value = (int) $sanitized_value; // rzutowanie na typ integer
                if ($parsed_value < $from_incl || $parsed_value > $to_incl) // jeśli nie mieści się w przedziale, error
                {
                    $error_message = '* Wartość musi znajdować się w granicach od ' . $from_incl . ' do ' . $to_incl . '.';
                }
            }
            else // jeśli nie jest liczbą, error
            {
                $error_message = '* Wartość pola musi być liczbą.';
            }
        }
        // zwróć tablicę asocjacyjną z informacjami po weryfikacji pola formularza
        return array(
            'value' => $sanitized_value,
            'error_message' => $error_message,
        );
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda parsująca globalną tablicę permisji w tagi option wysyłane do widoku.
    public static function parse_users_permissions($selected_value = '')
    {
        $permissions_html = ''; // skonkatenowany string
        foreach (Config::get('__ROLES') as $permission) // przejdź przez wszystkie role i stwórz tagi <option></option>
        {
            $selected = $selected_value === $permission ? 'selected' : '';
            $permissions_html .= '<option ' . $selected . ' value="' . $permission . '">' . ucfirst($permission) . '</option>';
        }
        return $permissions_html; // zwróć skonkatenowany string
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda sprawdzająca, czy w przesyłanej tablicy nie znajdują się wiadomości error.
    public static function check_if_form_is_invalid($form_data)
    {
        foreach ($form_data as $key => $value) // przejdź przez wszystkie wartości w tablicy
        {
            // jeśli jakiś tekst będzie w wiadomości o błędzie, zwróć true
            if (!empty($form_data[$key]['error_message'])) return true;
        }
        return false; // w przeciwnym wypadku wszystko OK i zwróć false
    }
}