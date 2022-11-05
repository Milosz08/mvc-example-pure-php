<?php

namespace App\Services;

// Serwis dla kontrolera AuthController odpowiadający za walidację.
class AuthService
{
    private $login_form_input = array('value' => '', 'error_message' => '');
    private $password_form_input = array('value' => '', 'error_message' => '');

    /**
     * Metoda zwracająca wartość true/false, jeśli wszystkie pola w formularzu logowania są poprawne.
     */
    public function form_data_is_correct()
    {
        return empty($this->login_form_input['error_message']) && empty($this->password_form_input['error_message']);
    }

    /**
     * Metoda walidująca wszystkie pola formularza logowania
     */
    public function validate_login_data()
    {
        $this->login_form_input = $this->validate_simple_form_input('login', 'user');
        $this->password_form_input = $this->validate_simple_form_input('password', 'pass');
    }

    /**
     * Metoda zwracająca dane walidacji formularza logowania.
     */
    public function get_validation_data()
    {
        return array(
            'login' => $this->login_form_input,
            'password' => $this->password_form_input,
        );
    }

    /**
     * Metoda odpowiedzialna za czyszczenie pól walidacji formularza logowania.
     */
    public function clear_validation_data()
    {
        $this->login_form_input = array('value' => '', 'error_message' => '');
        $this->password_form_input = array('value' => '', 'error_message' => '');
    }

    /**
     * Metoda do walidacji inputu tekstowego na podstawie preferowanej wartości.
     */
    private function validate_simple_form_input($input_name, $preferred_value)
    {
        $error_message = '';
        $sanitized_value = htmlspecialchars($_POST[$input_name]); // sanetyzacja pola przeciwko atakom XSS
        if (empty($sanitized_value)) // jeśli wartość jest pusta, error
        {
            $error_message = '* ' . ucfirst($input_name) . ' cannot be empty.';
        }
        else if ($sanitized_value != $preferred_value) // jeśli wartość jest inna niż preferowana, error
        {
            $error_message = '* Incorrect ' . $input_name . '.';
        }
        // zwróć tablicę asocjacyjną z informacjami po weryfikacji pola formularza
        return array(
            'value' => $sanitized_value,
            'error_message' => $error_message
        );
    }

    /**
     * Metoda walidująca dla inputu liczbowego przyjmująca nazwę inputu oraz zakres od
     * do którego ma walidować (zakres ten jest obustronnie otwarty).
     */
    private function validate_numer_form_input($input_name, $from_incl, $to_incl)
    {
        $error_message = '';
        // sanetyzacja pola przeciwko atakom XSS oraz filtrowanie wartości (filtr sanetyzujący liczby)
        $sanitized_value = filter_var($_POST[$input_name], FILTER_SANITIZE_NUMBER_INT);
        if (empty($_POST[$input_name])) // jeśli wartość jest pusta, error
        {
            $error_message = '* ' . ucfirst($input_name) . ' cannot be empty.';
        }
        else
        {
            if (is_numeric($sanitized_value)) // jeśli wartość jest liczbą, sprawdź czy mieści się w przedziale
            {
                $parsed_value = (int) $sanitized_value;
                if ($parsed_value <= $from_incl || $parsed_value >= $to_incl) // jeśli nie mieści się w przedziale, error
                {
                    $error_message = '* ' . ucfirst($input_name) . ' must be between ' . $from_incl . ' to ' . $to_incl . '.';
                }
            }
            else // jeśli nie jest liczbą, error
            {
                $error_message = '* ' . ucfirst($input_name) . ' must be number.';
            }
        }
        // zwróć tablicę asocjacyjną z informacjami po weryfikacji pola formularza
        return array(
            'value' => $sanitized_value,
            'error_message' => $error_message
        );
    }
}