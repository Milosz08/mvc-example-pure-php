<?php

namespace App\Services;

use PDO;
use Exception;
use App\Utils\Util;
use App\Core\Config;
use App\Core\Service;

// Klasa serwisu dla kontrolera UsersController.
class UsersService extends Service
{
    private static $_instance; // instancja klasy na podstawie wzorca singleton
    private $_form_data = array('first_name', 'last_name', 'login', 'age', 'role'); // tablica z polami dodawania/edytowania użytkownika

    protected function __construct()
    {
        parent::__construct();
        // automatyczne wypełnienie każdego pola dodatkową tablicą przechowującą poprzednią wartość i wiadomość błędu
        $this->_form_data = Util::fill_form_assoc($this->_form_data);
    }

    // Metoda umożliwiająca dodanie nowego użytkownia. Waliduje a następnie dodaje przy wykorzystaniu transakcji użytkownika do bazy danych
    // na podstawie parametrów formularza.
    public function create_new_user()
    {
        $this->validate_form_add_edit_user(); // wykonaj walidację elementów formularza
        if (Util::check_if_form_is_invalid($this->_form_data)) return; //sprawdź, czy formularz zawiera błędy, jeśli tak wyjdź z metody
        try
        {
            $this->_dbh->beginTransaction(); // rozpocznij transakcję
            $role_id = $this->get_role_id_based_name(); // przechwyć id roli
            $this->check_if_login_is_unique(); // sprawdź, czy nie nastąpiła próba dodania użytkownika z takim samym loginem

            // zapytanie sql umożliwiające dodanie nowego użytkownika do bazy danych
            $query = "INSERT INTO users (`first_name`, `last_name`, `login`, `password`, `age`, `role_id`) VALUES (?,?,?,?,?,?)";
            $statement = $this->_dbh->prepare($query); // przygotuj zapytanie dodające użytkownika
            $statement->execute(array( // wykonaj zapytanie ze wstrzykniętymi zmiennymi
                ucfirst($this->_form_data['first_name']['value']),
                ucfirst($this->_form_data['last_name']['value']),
                $this->_form_data['login']['value'],
                sha1(Config::get('__SHA1_SALT') . $this->_form_data['login']['value']), // zahaszuj hasło przy użyciu soli
                (int)($this->_form_data['age']['value']),
                (int)($role_id[0]),
            ));

            $this->_dbh->commit(); // zatwierdź transakcję
            header('Location:index.php?action=users/show'); // przekieruj do widoku tabeli użytkowników
        }
        catch (Exception $e)
        {
            $this->_banner_text = $e->getMessage(); // przypisanie do banera wiadomości błędu
            $this->_dbh->rollback(); // cofnięcie transakcji
        }
    }

    // Metoda odpowiadająca za edytowanie wybranego użytkownika na podstawie ID. Jeśli użytkownik nie istnieje, error. Metoda również nie
    // pozwala na przypisanie użytkownikowi rangi czytelnik, jeśli w systemie nie będzie żadnego administratora.
    public function edit_existing_user($user_id)
    {
        $this->validate_form_add_edit_user(); // wykonaj walidację elementów formularza
        if (Util::check_if_form_is_invalid($this->_form_data)) return; //sprawdź, czy formularz zawiera błędy, jeśli tak wyjdź z metody
        try
        {
            $this->_dbh->beginTransaction(); // rozpocznij transakcję

            $role_id = $this->get_role_id_based_name(); // przechwyć id roli
            $this->check_if_login_is_unique( // sprawdź, czy nie nastąpiła próba edycji użytkownika z takim samym loginem
                "SELECT COUNT(id) FROM users WHERE `login` = ? AND NOT id = ?", array((int)$user_id)
            );
            // jeśli w systemie istnieje tylko jeden administrator, a użytkownik ma ustawiony tryb czytelnik nie pozwól na jego zmianę
            if ($this->_form_data['role']['value'] === Config::get('__USER_ROLE')) $this->is_one_system_administrator();

            // zapytanie sql umożliwiające edycję wybranego użytkownika na podstawie wartości id
            $query = "
                UPDATE users SET `first_name` = ?, `last_name` = ?, `login` = ?, `password` = ?, `age` = ?, `role_id` = ?
                WHERE id = ?
            ";
            $statement = $this->_dbh->prepare($query); // przygotuj zapytanie aktualizujące użytkownika
            $statement->execute(array( // wykonaj zapytanie ze wstrzykniętymi zmiennymi
                ucfirst($this->_form_data['first_name']['value']),
                ucfirst($this->_form_data['last_name']['value']),
                $this->_form_data['login']['value'],
                sha1(Config::get('__SHA1_SALT') . $this->_form_data['login']['value']), // zahaszuj hasło przy użyciu soli
                (int)($this->_form_data['age']['value']),
                (int)($role_id[0]),
                (int)($user_id),
            ));

            $this->_dbh->commit(); // zatwierdź transakcję
            header('Location:index.php?action=users/show'); // przekieruj do widoku tabeli użytkowników
        }
        catch (Exception $e)
        {
            $this->_banner_text = $e->getMessage(); // przypisanie do banera wiadomości błędu
            $this->_dbh->rollback(); // cofnięcie transakcji
        }
    }

    // Metoda umożliwiająca usuwanie użytkownika z systemu. Nie pozwala na usunięcie administratora, jeśli jest on jeden w systemie.
    // Przed usunięciem użytkownika sprawdza, czy nie miał wypożyczonych książek, jeśli miał, książki dodają się do tabeli książek
    public function remove_user($user_id)
    {
        try
        {
            $this->_dbh->beginTransaction(); // rozpocznij transakcję

            // przygotuj zapytanie pobierające książki usuwanego użytkownika
            $statement = $this->_dbh->prepare("SELECT book_id FROM books_users_binding WHERE `user_id` = ?");
            $statement->execute(array((int)$user_id)); // wykonaj zapytanie
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) // przejdź przez wszystkie pobrane id książek
            {
                // przygotuj zapytanie do zwracania wypożyczonych książek z tabeli usuwanego użytkownika
                $return_statement = $this->_dbh->prepare("UPDATE books SET `copies` = `copies` + 1 WHERE id = ?");
                if (!$return_statement->execute(array($row['book_id']))) { // zinkrementuj książkę, jeśli błąd error
                    throw new Exception('Nie udało się zaktualizowac listy książek. Spróbuj ponownie.');
                }
            }
            // przygotuj zapytanie sprawdzające, czy dany użytkownik istnieje oraz czy można go usunąć
            $query = "
                SELECT users.id AS id, roles.name AS `user_role` FROM users INNER JOIN roles ON users.role_id = roles.id WHERE users.id = ?
            ";
            $statement = $this->_dbh->prepare($query); // przygotowanie zapytania sprawdzająceg czy użytkownik istnieje
            $statement->execute(array((int)$user_id)); // wykonanie zapytania
            $found_user_data = $statement->fetch(PDO::FETCH_ASSOC); // przypisanie rezultatu zapytania do zmiennej
            $statement->closeCursor(); // zwolnienie zasobów
            if (!$found_user_data) { // jeśli nie znaleziono użytkownika, błąd
                throw new Exception('Wybrany użytkownik nie istnieje lub nie jest możliwe jego usunięcie.');
            }
            // sprawdź, czy nie dochodzi do próby usunięcia samego siebie z systemu
            if ($_SESSION['logged_user']['user_id'] == $found_user_data['id'])
            {
                throw new Exception('Zalogowany użytkownik nie może sam siebie usunąć.');
            }
            // jeśli w systemie istnieje tylko jeden administrator, nie pozwól na jego usunięcie
            if ($found_user_data['user_role'] === Config::get('__ADMIN_ROLE')) $this->is_one_system_administrator();

            // usuń użytkownika
            $statement = $this->_dbh->prepare("DELETE FROM users WHERE id = ?"); // przygotuj zapytanie SQL
            $statement->execute(array($_GET['_userid'])); // wykonanie zapytania SQL

            $this->_dbh->commit(); // zatwierdź transakcję
            $this->_banner_text = 'Pomyślnie usunięto użytkownika z id ' . $user_id . ' z bazy danych.';
        }
        catch (Exception $e)
        {
            $this->_banner_error = true; // przypisanie true do flagi error
            $this->_banner_text = $e->getMessage(); // przypisanie do banera wiadomości błędu
            $this->_dbh->rollback(); // cofnięcie transakcji
        }
    }

    // Metoda pobierająca rolę z bazy danych na podstawie nazwy i zwracająca id. Jeśli nie znajdzie, exception.
    public function get_role_id_based_name()
    {
        $statement = $this->_dbh->prepare("SELECT id FROM roles WHERE `name` = ?"); // zapytanie znajdujące id roli
        $statement->execute(array($this->_form_data['role']['value'])); // wykonaj zapytanie znajdujące id roli
        $role_id = $statement->fetch(); // zapisz wartość id roli w zmiennej
        $statement->closeCursor(); // zwolnienie zasobów
        if (empty($role_id)) // jeśli nie znajdzie roli, wyjdź z metody i cofnij transakcję
        {
            throw new Exception('Nie znaleziono roli: "' . $this->_form_data['role']['value'] . '". Spróbuj ponownie.');
        }
        return $role_id;
    }

    // Metoda sprawdzająca czy podany przez administratora login przy tworzeniu/edytowaniu użytkownika jest poprawny.
    // Jeśli nie jest, exception.
    public function check_if_login_is_unique($query = null, $data_array = array())
    {
        if ($query == null && empty($data_array)) // jeśli nie podano parametrów, potraktuj jakby dodawano nowego użytkownika
        {
            $query = "SELECT COUNT(id) FROM users WHERE `login` = ?";
        }
        array_push($data_array, $this->_form_data['login']['value']); // dodaj parametr loginu
        $statement = $this->_dbh->prepare($query); // przygotuj zapytanie
        $statement->execute($data_array); // wykonaj zapytanie
        if ((int)$statement->fetch(PDO::FETCH_NUM)[0] > 0) // jeśli znajdzie innego użytkownika z takim samym loginem, error
        {
            $statement->closeCursor(); // zwolnienie zasobów
            throw new Exception('Użytkownik z podanym loginem istnieje już w systemie. Spróbuj ponownie z innym loginem.');
        }
        $statement->closeCursor(); // zwolnienie zasobów
    }

    // Metoda umożliwiająca sprawdzenie, ile w systemie istnieje kont z rangą administratora. Jeśli jest tylko jedno konto, zwraca true i
    // wiadomość błędu informującą, że nie można usunąć jednego administratora
    private function is_one_system_administrator()
    {
        // zapytanie sprawdzające, ile w systemie istnieje administratorów
        $query = "SELECT COUNT(users.id) FROM users INNER JOIN roles ON users.role_id = roles.id WHERE roles.name = ?";
        $statement = $this->_dbh->prepare($query); // przygotuj zapytanie SQL
        $statement->execute(array(Config::get('__ADMIN_ROLE'))); // wykonanie zapytania SQL
        if ($statement->fetch(PDO::FETCH_NUM)[0] < 2) // jeśli kont adminów jest mniej niż 2, nie pozwój na usunięcie
        {
            $statement->closeCursor(); // zwolnienie zasobów
            throw new Exception('Nie można usunąć z systemu jednego administratora.');
        }
        $statement->closeCursor(); // zwolnienie zasobów
    }

    // Metoda odpowiadająca za walidowanie formularza wprowadzania nowego użytkownika oraz edycji istniejącego.
    private function validate_form_add_edit_user()
    {
        $user_name_surname_pattern = '/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]{2,30}$/'; // wzór regex przyjmujący tylko litery (min 2, max 30 znaków)
        $this->_form_data['first_name'] = Util::validate_regex_field('first_name', $user_name_surname_pattern); // sprawdź, czy imię jest poprawne
        $this->_form_data['last_name'] = Util::validate_regex_field('last_name', $user_name_surname_pattern); // sprawdź, czy nazwisko jest poprawne
        $this->_form_data['login'] = Util::validate_regex_field('login', '/^[a-z0-9]{2,20}$/'); // sprawdź, czy login jest poprawny
        $this->_form_data['role'] = Util::check_if_input_not_empty('role'); // sprawdź, czy uprawnienia/rola nie są puste
        $this->_form_data['age'] = Util::validate_number_field('age', 13, 100); // sprawdź, czy wiek mieści się w przedziale
    }

    // Metoda odpowiadająca za przypisanie wartości z modelu użytkownika do tablicy inputów. Niezbędna do zachowania treści po odświeżeniu
    // przeglądarki poprzez wysłanie requesta POST z danymi formularza.
    public function add_user_values_from_query($user_model)
    {
        $this->_form_data['first_name'] = array('value' => $user_model->get_first_name(), 'error_message' => '');
        $this->_form_data['last_name'] = array('value' => $user_model->get_last_name(), 'error_message' => '');
        $this->_form_data['login'] = array('value' => $user_model->get_login(), 'error_message' => '');
        $this->_form_data['age'] = array('value' => $user_model->get_age(), 'error_message' => '');
        $this->_form_data['role'] = array('value' => $user_model->get_role_name(), 'error_message' => '');
    }

    // Metoda zwracająca szczegółowe informacje na temat walidacji pól formularza dodawania/edycji użytkownika
    public function get_form_validatior_user()
    {
        return $this->_form_data;
    }

    // Instantancja obiektu typu singleton
    public static function get_instance()
    {
        if (self::$_instance == null) self::$_instance = new UsersService();
        return self::$_instance;
    }
}
