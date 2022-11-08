<?php

namespace App\Services;

use Exception;
use App\Utils\Util;
use App\Core\Config;
use App\Core\Service;
use App\Models\LoginUserModel;

// Serwis dla kontrolera AuthController odpowiadający za walidację.
class AuthService extends Service
{
    private static $_instance; // instancja klasy na podstawie wzorca singleton
    private $_form_data = array('login', 'password'); // tablica wartości z formularza logowania

    //--------------------------------------------------------------------------------------------------------------------------------------

    protected function __construct()
    {
        parent::__construct(); // wywołanie konstruktora klasy nadrzędnej
        // automatyczne wypełnienie każdego pola dodatkową tablicą przechowującą poprzednią wartość i wiadomość błędu
        $this->_form_data = Util::fill_form_assoc($this->_form_data);
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda umożliwiająca zalogowanie użytkownia. Sprawdza, czy użytkownik istnieje, jeśli nie przekazuje błąd do kontrolera.
    // Jeśli natomiast użytkownik istnienie, zapisu nowy obiekt w sesji i przenosi do chronionych zasobów serwera.
    public function login_user()
    {
        $this->_form_data['login'] = Util::validate_regex_field('login', '/^[a-z0-9]{2,20}$/'); // sprawdź, czy login jest poprawny
        $this->_form_data['password'] = Util::check_if_input_not_empty('password'); // sprawdź, czy hasło nie jest puste
        if (Util::check_if_form_is_invalid($this->_form_data)) return; //sprawdź, czy formularz zawiera błędy, jeśli tak wyjdź z metody
        try
        {
            // zapytanie pobierające użytkownika na podstawie loginu oraz zahaszowanego hasła
            $query = "
                SELECT users.id, `login`, roles.name AS `user_role`, CONCAT(first_name, ' ', last_name) AS `full_name`
                FROM users INNER JOIN roles ON users.role_id = roles.id
                WHERE `login` = ? AND `password` = ?
            ";
            $statement = $this->_dbh->prepare($query); // przygotuj zapytanie
            $statement->execute(array( // wykonanie zapytania SQL
                $this->_form_data['login']['value'],
                sha1(Config::get('__SHA1_SALT') . $this->_form_data['login']['value']), // zahaszuj hasło przy użyciu soli
            ));

            $find_user = $statement->fetchObject(LoginUserModel::class); // zmapuj otrzymane dane na obiekt
            // jeśli nie znajdzie rzuć wyjątek
            if (empty($find_user)) throw new Exception();
        
            // jeśli znajdzie użytkownika, przejdź do procedury zapisywania stanu sesji i przejścia do chronionych zasobów serwera
            $_SESSION['logged_user'] = array(  // przypisz użytkownika do sesji serwera
                'user_id' => $find_user->get_id(),
                'user_role' => $find_user->get_user_role(),
                'full_name' => $find_user->get_full_name(),
            );
            // jeśli użytkownik jest zalogowany, przekieruj do sekcji dla odpowiedniej roli
            $redir_location = $_SESSION['logged_user']['user_role'] == Config::get('__ADMIN_ROLE') ? 'users' : 'books';
            header('Location:index.php?action=' . $redir_location . '/show'); // przekierowanie na adres
            ob_end_flush(); // zwolnienie bufora
        }
        catch (Exception $e)
        {
            $this->_banner_text = 'Nieprawidłowy login i/lub hasło. Spróbuj ponownie wprowadzając inne dane.';
        }
        finally // wykonaj niezależnie, czy został przechwycony wyjątek czy nie
        {
            $statement->closeCursor(); // zwolnij zasoby
        }
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda sprawdzająca, czy użytkownik jest zalogowany z jakąś rangą. Jeśli tak, przekierowanie na wybraną stronę.
    public function redirect_only_for_logged()
    {
        if ($_SESSION['logged_user'] == null) return; // jeśli użytkownik nie jest zalogowany, nie wykonuj przekierowań
        // jeśli użytkownik jest zalogowany z rolą administratora, przekieruj do sekcji dla administratora
        if ($_SESSION['logged_user']['user_role'] == Config::get('__ADMIN_ROLE'))
        {
            header('Location:index.php?action=users/show'); // przekierowanie na adres
            ob_end_flush(); // zwolnienie bufora
        }
        // jeśli użytkownik jest zalogowany z rolą czytelnika (użytkownika) przekieruj do sekcji dla czytelników
        if ($_SESSION['logged_user']['user_role'] == Config::get('__USER_ROLE'))
        {
            header('Location:index.php?action=books/show'); // przekierowanie na adres
            ob_end_flush(); // zwolnienie bufora
        }
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda zwracająca elementy formularza jako tablicę wartości i wiadomości błędów
    public function get_form_validatior_auth()
    {
        return $this->_form_data;
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Instantancja obiektu typu singleton
    public static function get_instance()
    {
        if (self::$_instance == null) self::$_instance = new AuthService();
        return self::$_instance;
    }
}