<?php

namespace App\Core;

// Główna klasa abstrakcyjna kontrolerów. Każdy kontroler musi rozszerzać tą klasę.
abstract class Controller
{
    protected $renderer; // instancja klasy do renderowania widoków
    protected $_pdo; // instancja klasy PDO
    protected $_dbh; // handler do bazy danych (PDO)
    
    //--------------------------------------------------------------------------------------------------------------------------------------

    public function __construct()
    {
        $this->renderer = Renderer::get_instance();
        $this->_pdo = DbContext::get_instance();
        $this->_dbh = $this->_pdo->get_handler();
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda generująca główny widok kontrolera.
    abstract function index();
}