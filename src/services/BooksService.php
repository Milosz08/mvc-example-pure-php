<?php

namespace App\Services;

use PDO;
use Exception;
use App\Utils\Util;
use App\Core\Config;
use App\Core\Service;
use App\Core\DbContext;
use App\Models\UserModel;
use App\Models\BookModel;
use App\Models\RentedBookModel;
use App\Models\RentedBookViewModel;

// Klasa serwisu dla kontrolera BooksController.
class BooksService extends Service
{
    private static $_instance; // instancja klasy na podstawie wzorca singleton
    private $_form_data = array('title', 'authors', 'copies'); // tablica wartości z formularza dodawania/edycji książki

    //--------------------------------------------------------------------------------------------------------------------------------------

    protected function __construct()
    {
        parent::__construct(); // wywołanie konstruktora klasy nadrzędnej
        // automatyczne wypełnienie każdego pola dodatkową tablicą przechowującą poprzednią wartość i wiadomość błędu
        $this->_form_data = Util::fill_form_assoc($this->_form_data);
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda zapełniająca wartości formularza nadesłane z bazy danych na podstawie zmapowanego obiektu.
    public function add_book_values_from_query($book_model)
    {
        $this->_form_data['title'] = array('value' => $book_model->get_title(), 'error_message' => '');
        $this->_form_data['authors'] = array('value' => $book_model->get_authors(), 'error_message' => '');
        $this->_form_data['copies'] = array('value' => $book_model->get_copies(), 'error_message' => '');
    }
    
    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda umożliwiająca stworzenie nowej książki. Sprawdza czy nie doszła próba dodania duplikatu (na podstawie tytułu).
    public function create_new_book()
    {
        $this->_form_data['title'] = Util::check_if_input_not_empty('title'); // sprawdź, czy tytuł książki nie jest pusty
        $this->_form_data['authors'] = Util::check_if_input_not_empty('authors'); // sprawdź, czy pole autorzy nie jest puste
        $this->_form_data['copies'] = Util::validate_number_field('copies', 1, 10000); // sprawdź, czy liczba egzemplarzy mieści się w przedziale
        if (Util::check_if_form_is_invalid($this->_form_data)) return; // jeśli formularz zawiera błędy, wyjdź z funkcji i wyświetl błędy
        try
        {
            $this->_dbh->beginTransaction(); // rozpoczęcie transakcji

            // przygotuj zapytania sprawdzające, czy jest duplikat
            $statement = $this->_dbh->prepare("SELECT COUNT(id) FROM books WHERE LOWER(title) = :title");
            $statement->execute(array('title' => strtolower($this->_form_data['title']['value']))); // wykonanie komendy
            if (((int)$statement->fetch(PDO::FETCH_NUM)[0]) > 0) // jeśli wartość jest większa od 0, error
            {
                throw new Exception('Książka z podanym tytułem istnieje już w systemie. Spróbuj ponownie dodać inną książkę.'); 
            }
            // przygotuj zapytanie dodające nową książkę
            $statement = $this->_dbh->prepare("INSERT INTO books (title, authors, copies) VALUES (?,?,?)");
            $statement->execute(array( // wykonanie kwerendy
                $this->_form_data['title']['value'],
                $this->_form_data['authors']['value'],
                $this->_form_data['copies']['value'],
            ));

            $this->_dbh->commit(); // zatwierdzenie transakcji
        }
        catch (Exception $e)
        {
            $this->_banner_text = $e->getMessage(); // przypisz wiadomość błędu do bannera
            $this->_dbh->rollback(); // cofnij transakcję
            return;
        }
        header('Location:index.php?action=books/show'); // przekierowanie na adres
        ob_end_flush(); // zwolnienie bufora
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Uniwersalna metoda do pożyczania, oddawania książki. Na podstawie parametrów oraz kwerend następuje oddanie bądź pożyczenie książki.
    public function rent_refund_selected_book($mode_descr_first, $mode_descr_second, $first_query, $second_query, $is_rent = false)
    {
        // jeśli książka z podanym id nie istnieje, przekieruj do index.php?action=books/show
        $existing_book = $this->_pdo->check_if_exist("SELECT * FROM books WHERE id = :id_sant", BookModel::class, 'book', 'books', 'show');
        try
        {
            $this->_dbh->beginTransaction(); // rozpoczęcie transakcji
            // sprawdź, czy nie następuje próba pożyczenia książki, których ilość wynosi 0 (tylko dla wypożyczenia)
            if ($existing_book->get_copies() == 0 && $is_rent) throw new Exception('Brak książek na stanie możliwych do wypożyczenia.'); 
            
            // przygotuj zapytanie wstawiające/usuwające id użytkownika i książki do/z tabeli łączonej
            $statement = $this->_dbh->prepare($first_query);
            $add_status = $statement->execute(array($_SESSION['logged_user']['user_id'], $existing_book->get_id())); // wykonanie komendy
            // jeśli nie uda się zmienić danych książki do tabeli łączonej, wyrzuć wyjątek
            if (!$add_status) throw new Exception('Nieudane ' . $mode_descr_first . ' książki. Spróbuj ponownie.');
           
            // przygotuj zapytanie dekrementujące/inkrementujące ilość dostępnych książek na podstawie id książki
            $statement = $this->_dbh->prepare($second_query);
            $update_status = $statement->execute(array($existing_book->get_id())); // wykonanie komendy
            // jeśli nie uda się zdekrementować ilości książek, wyrzuć wyjątek
            if (!$update_status) throw new Exception('Nieudane ' . $mode_descr_first . ' książki. Spróbuj ponownie.');

            // wyświetl komunikat o poprawnym wypożyczeniu książki
            $this->_banner_text = 'Książka "' . $existing_book->get_title() . '" została ' . $mode_descr_second . ' przez użytkownika "' . 
                                   $_SESSION['logged_user']['full_name'] . '".';
            $this->_banner_error = false;

            $this->_dbh->commit(); // zatwierdzenie transakcji
        }
        catch (Exception $e)
        {
            $this->_banner_error = true;
            $this->_banner_text = $e->getMessage();
            $this->_dbh->rollback(); // cofnij transakcję
        }
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda sprawdzająca, czy użytkownik przechodzący na stronę z wypożyczonymi książkami jest administratorem bądź czytelnikiem i 
    // zwracająca obiekt znalezionego użytkownika na podstawie id. Jeśli nie znajdzie użytkownika, przekierowanie na wybrany adres.
    public function check_authentication_values($skip_redirect = false)
    {
        $is_admin = $_SESSION['logged_user']['user_role'] === Config::get('__ADMIN_ROLE'); // czy zalogowany użytkownik jest administratorem
        $is_user = $_SESSION['logged_user']['user_role'] === Config::get('__USER_ROLE'); // czy zalogowany użytkownik jest czytelnikiem
        $redir_path = $is_admin ? 'users' : 'books'; // ścieżka przekierowania w przypadku błędu (nazwa kontrolera)
        // jeśli nie podano parametru i użytkownik jest administratorem, lub podano parametr a użytkownik jest czytelnikiem, przekieruj
        if (((!isset($_GET['_userid']) && $is_admin) || (isset($_GET['_userid']) && $is_user)) && !$skip_redirect)
        {
            header('Location:index.php?action=' . $redir_path . '/show'); // przekierowanie na adres
            ob_end_flush(); // zwolnienie bufora
        }

        if ($is_admin && !$skip_redirect) $books_user_id = (int)$_GET['_userid']; // przypisz z parametru, jeśli wchodzi administrator
        // przypisz z sesji, jeśli wchodzi czytelnik lub administrator w trybie oddawania książki
        if ($is_user || $skip_redirect) $books_user_id = $_SESSION['logged_user']['user_id'];

        // sprawdź, czy użytkownik z zapisanym ID istnieje
        $found_user = $this->_pdo->check_if_exist(DbContext::GET_USER_QUERY, UserModel::class, $books_user_id, $redir_path, 'show');
        return array(
            'user' => $found_user, // znaleziony model użytkownika
            'is_self_user' => $_SESSION['logged_user']['user_id'] == $books_user_id, // czy użytkownik edytuje sam siebie
            'user_full_name' => $is_admin ? $found_user->get_full_name() : $_SESSION['logged_user']['full_name'], // nazwa w headerze
        );
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda zwracająca wszystkie książki (bazując na klasie modelu książki) z bazy danych konkretnego użytkownika wyszukiwanego po id.
    public function return_rents_books($user_model)
    {
        $user_rented_books = array(); // tablica wynikowa z wypożyczonymi książkami użytkownika
        try
        {
            $this->_dbh->beginTransaction(); // rozpocznij transakcję
            // zapytanie łączące dane z tablicy łączącej i wyszukujące wszystkie wypożyczone książki użytkownika wyszukiwanego po id
            $query = "
                SELECT DISTINCT books.id AS id, books.title AS title, books.authors AS authors FROM books_users_binding
                INNER JOIN books ON books_users_binding.book_id = books.id
                INNER JOIN users ON books_users_binding.user_id = users.id
                WHERE books_users_binding.user_id = ?
            ";
            $statement = $this->_dbh->prepare($query); // przygotuj zapytanie
            $statement->execute(array($user_model->get_id())); // wykonaj zapytanie
            while($fetched_rented_book = $statement->fetchObject(RentedBookModel::class)) // przejdź przez wszystkie rekordy i zmapuj
            {
                // zapytanie pobierające liczbę wypożyczonej konkretnej książki przez użytkownika
                $count_query = "
                    SELECT COUNT(books_users_binding.book_id) FROM books_users_binding
                    WHERE books_users_binding.user_id = ? AND books_users_binding.book_id = ?
                ";
                $count_statement = $this->_dbh->prepare($count_query); // przygotuj zapytanie
                $count_statement->execute(array($user_model->get_id(), $fetched_rented_book->get_id())); // wykonaj zapytanie

                $count_of_single_book = $count_statement->fetch(PDO::FETCH_NUM)[0]; // liczba wystąpień pojedynczej książki
                array_push($user_rented_books, new RentedBookViewModel($fetched_rented_book, $count_of_single_book)); // dodaj do tablicy
            }
            $this->_dbh->commit(); // zatwierdź transakcję
        }
        catch(Exception $e)
        {
            $this->_banner_error = true;
            $this->_banner_text = 'Nie udało się pobrać wypożyczonych książek użytkownika "' . $user_model->get_full_name() . '".';
            $this->_dbh->rollback(); // cofnij transakcję
        }
        finally // wykonaj niezależnie, czy został przechwycony wyjątek czy nie
        {
            $statement->closeCursor(); // zwolnij zasoby
        }
        return $user_rented_books;
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda zwracająca elementy formularza jako tablicę wartości i wiadomości błędów
    public function get_form_validatior_book()
    {
        return $this->_form_data;
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Instantancja obiektu typu singleton
    public static function get_instance()
    {
        if (self::$_instance == null) self::$_instance = new BooksService();
        return self::$_instance;
    }
}