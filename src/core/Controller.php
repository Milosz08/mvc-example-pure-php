<?php

// Główna klasa abstrakcyjna kontrolerów. Każdy kontroler musi rozszerzać tą klasę.
abstract class Controller
{
    protected $renderer; // instancja klasy do renderowania widoków
    
    //--------------------------------------------------------------------------------------------------------------------------------------

    public function __construct()
    {
        $this->renderer = Renderer::get_instance();
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    // Metoda generująca główny widok kontrolera.
    abstract function index();
}