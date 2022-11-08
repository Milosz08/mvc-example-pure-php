<?php

namespace App\Models;

// Klasa modelu reprezentująca encję wypożyczonej książki przez użytkownika
class RentedBookModel
{
    protected $id; // kolumna z id wypożyczonej książki
    protected $title; // kolumna z tytułem wypożyczonej książki
    protected $authors; // kolumna z autorami wypożyczonej książki

    //--------------------------------------------------------------------------------------------------------------------------------------

    public function get_id()
    {
        return $this->id;
    }

    public function get_title()
    {
        return $this->title;
    }

    public function get_authors()
    {
        return $this->authors;
    }
}

//------------------------------------------------------------------------------------------------------------------------------------------

// Klasa modelu przekazywana do widoku rozszerzająca podstawowy model mapowany i zapełniany danymi przez PDO z bazy danych
class RentedBookViewModel extends RentedBookModel
{
    protected $rented_count; // przeliczona kolumna z ilością wypożyczonych książek (komenda SQL)

    //--------------------------------------------------------------------------------------------------------------------------------------

    // konstruktor przepisujący wartości z klasy nadrzędnej oraz ilości wypożyczonych książek
    public function __construct($rented_book_model, $rented_count)
    {
        $this->id = $rented_book_model->get_id();
        $this->title = $rented_book_model->get_title();
        $this->authors = $rented_book_model->get_authors();
        $this->rented_count = $rented_count;
    }

    //--------------------------------------------------------------------------------------------------------------------------------------

    public function get_rented_count()
    {
        return $this->rented_count;
    }
}