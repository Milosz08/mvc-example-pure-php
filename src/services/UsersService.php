<?php

namespace App\Services;

use Exception;
use App\Core\Config;

// Klasa serwisu dla kontrolera UsersController.
class UsersService
{
    // tablica z polami formularza dodawania/edytowania użytkownika
    private $_form_data = array('first_name', 'last_name', 'login', 'age', 'permission');

    //--------------------------------------------------------------------------------------------------------------------------------------

    public function __construct()
    {
        // automatyczne wypełnienie każdego pola dodatkową tablicą przechowującą poprzednią wartość i wiadomość błędu
        $this->_form_data = array_fill_keys($this->_form_data, array('value' => '', 'error_message' => ''));
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    public function insert_edit_values($values)
    {
        if (count($values) != count($this->_form_data))
        {
            throw new Exception('Inserting values table must have the same length of initial input values table.');
        }
        $iterator = 0;
        foreach ($this->_form_data as $key => $value)
        {
            $this->_form_data[$key] = array('value' => $values[$iterator++], 'error_message' => '');
        }
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    public function validate_form_add_edit_user($values)
    {

        $_form_data['age'] = $this->validate_age_field($_POST['age'], 13, 100);
    }
    
    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda walidująca dla inputu liczbowego przyjmująca nazwę inputu oraz zakres od do którego ma walidować (zakres ten jest 
    // obustronnie otwarty).
    private function validate_age_field($value, $from_incl, $to_incl)
    {
        $error_message = '';
        // sanetyzacja pola przeciwko atakom XSS oraz filtrowanie wartości (filtr sanetyzujący liczby)
        $sanitized_value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        if (empty($value)) // jeśli wartość jest pusta, error
        {
            $error_message = '* Wiek użytkownika nie może być pusty.';
        }
        else
        {
            if (is_numeric($sanitized_value)) // jeśli wartość jest liczbą, sprawdź czy mieści się w przedziale
            {
                $parsed_value = (int) $sanitized_value;
                if ($parsed_value <= $from_incl || $parsed_value >= $to_incl) // jeśli nie mieści się w przedziale, error
                {
                    $error_message = '* Wiek użytkownika musi być w granicach od ' . $from_incl . ' do ' . $to_incl . ' lat.';
                }
            }
            else // jeśli nie jest liczbą, error
            {
                $error_message = '* Wartość wieku użytkownika musi być liczbą.';
            }
        }
        // zwróć tablicę asocjacyjną z informacjami po weryfikacji pola formularza
        return array(
            'value' => $sanitized_value,
            'error_message' => $error_message
        );
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda zwracająca szczegółowe informacje na temat walidacji pól formularza dodawania/edycji użytkownika
    public function get_form_validatior_user()
    {
        return $this->_form_data;
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda parsująca globalną tablicę permisji w tagi option wysyłane do widoku
    public function parse_users_permissions()
    {
        $permissions_html = '';
        foreach (Config::get('__ROLES') as $permission)
        {
            $permissions_html .= '<option value="' . $permission . '">' . ucfirst($permission) . '</option>';
        }
        return $permissions_html;
    }
}