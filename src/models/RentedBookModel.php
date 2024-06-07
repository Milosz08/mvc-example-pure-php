<?php

namespace App\Models;

// Klasa modelu reprezentująca encję wypożyczonej książki przez użytkownika
class RentedBookModel
{
    protected $id; // kolumna z id wypożyczonej książki
    protected $title; // kolumna z tytułem wypożyczonej książki
    protected $authors; // kolumna z autorami wypożyczonej książki
    protected $rented_count; // przeliczona kolumna z ilością wypożyczonych książek (komenda SQL)

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

    public function get_rented_count()
    {
        return $this->rented_count;
    }
}
