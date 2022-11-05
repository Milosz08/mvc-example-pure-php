<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;

/**
 * Kontroler odpowiedzialny za akcję z logowaniem/wylogowywaniem użytkownika.
 */
class AuthController extends Controller
{
    private $_service; // zmienna dla instancji klasy serwisu

    //------------------------------------------------------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct(); // wywołanie konstruktora klasy rodzica (klasa abstrakcyjna Controller)
        $this->_service = new AuthService(); // stworzenie instancji serwisu
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Endpoint pod akcją "index.php?action=auth". Tożsame z endpoitem "index.php?action=auth/login".
     * Metoda umożliwia zalogowanie oraz sprawdzenie stanu aktywnej sesji użytkownika.
     */
    public function index()
    {        
        // jeśli przesłano dane w formularzu, przejdź do walidacji
        if (isset($_POST['login']) && isset($_POST['password']))
        {
            $this->_service->validate_login_data();
            
        }
        else // przekierowanie na stronę z grafiką w przypadku wejścia bez logowania
        {
            // przekierowanie w przypadku, gdy użytkownik jest już zalogowany
        }
        $this->renderer->render('login', array(
            'self_redirect' => $this->get_redir_self_login_location(),
            'form_data' => $this->_service->get_validation_data(),
        ));
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Endpoint pod akcją "index.php?action=auth/logout" wylogowywujący użytkownika i niszczący sesję.
     */
    public function logout()
    {
        $this->_service->clear_validation_data();
        $_SESSION['logged_user_details'] = null;
        session_destroy();
        $this->renderer->render('login', array(
            'self_redirect' => $this->get_redir_self_login_location(),
            'form_data' => $this->_service->get_validation_data(),
        ));
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Metoda pośrednicząca (dla endpointu "index.php?action=auth/login").
     * Równoznaczna z endpointem "index.php?action=auth".
     */
    public function login()
    {
        $this->index();
    }
    
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Metoda zwracająca adres URL na podstawie aktualnej ścieżki serwera i parametru auth/login.
     */
    private function get_redir_self_login_location()
    {
        return filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_URL) . '?action=auth/login';
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Metoda przekierowująca do strony zdjęcia w momencie gdy użytkownik jest już zalogowany
     */
    private function redirect_when_logged()
    {
        if (isset($_SESSION['logged_user_details']))
        {
            header('Location:index.php?action=gfx'); // przekierowanie
            ob_end_flush(); // wyłączenie buforowania
        }
    }
}