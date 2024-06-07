<?php

namespace App\Controllers;

use App\Core\Config;
use App\Core\Controller;
use App\Services\AuthService;

// Kontroler odpowiedzialny za akcję z logowaniem/wylogowywaniem użytkownika.
class AuthController extends Controller
{
    private const LOGIN_OP_PERFORMED = 'auth_login_op_performed'; // wyzwalacz przesłania formularza umożliwiającego zalogowanie
    private $_service; // instancja serwisu

    public function __construct()
    {
        parent::__construct(); // wywołanie konstruktora klasy rodzica (klasa abstrakcyjna Controller)
        $this->_service = AuthService::get_instance(); // pobranie obiektu typu singleton z serwisu
    }

    // Metoda umożliwiająca zalogowanie użytkownika. Na podstawie danych, tworzy obiekt sesji i przekierowuje do zabezpieczonych sekcji.
    // Alias dla endpointu index.php?action=auth/login. Równoznaczny z endpointem index.php?action=auth.
    public function login()
    {
        // przekierowanie w przypadku, gdy użytkownik jest już zalogowany, jeśli nie pozostanie na stronie
        $this->_service->redirect_only_for_logged();
        if (isset($_POST[self::LOGIN_OP_PERFORMED])) // jeśli przesłano dane w formularzu, przejdź do walidacji
        {
            $this->_service->login_user(); // zaloguj użytkownika i przejdź na adres chronionego zasobu
        }
        $this->renderer->render('login', array(
            'self_redirect' => Config::get('__SELF_SANITIZED') . '?action=auth/login',
            'op_performed' => self::LOGIN_OP_PERFORMED,
            'form_data' => $this->_service->get_form_validatior_auth(),
            'banner_text' => $this->_service->get_banner_text(),
            'banner_active_class' => !empty($this->_service->get_banner_text()) ? 'app__banner--enabled' : '',
        ));
    }

    // Metoda umożliwiająca wylogowanie użytkownika i niszcząca sesję.
    // Alias dla endpointu index.php?action=auth/logout.
    public function logout()
    {
        $_SESSION['logged_user'] = null; // usuń dane użytkownika
        session_destroy(); // zniszcz sesję
        $this->index(); // przekieruj na adres poprzez metodę pośredniczącą
    }

    // Metoda umożliwiająca zalogowanie użytkownika. Na podstawie danych, tworzy obiekt sesji i przekierowuje do zabezpieczonych sekcji.
    // Alias dla endpointu index.php?action=auth. Równoznaczny z endpointem index.php?action=auth/login.
    public function index()
    {
        header('Location:index.php?action=auth/login'); // przekierowanie na adres
        ob_end_flush(); // zwolnienie bufora
    }
}
