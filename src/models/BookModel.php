<?php

namespace App\Models;

// Klasa modelu dla encji "books" w bazie danych.
class BookModel
{
    protected $id; // id książki w bazie danych
    protected $title; // kolumna z tytułem
    protected $authors; // kolumna z autorami
    protected $copies; // kolumna z liczbą egzemplarzy

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

    public function get_copies()
    {
        return $this->copies;
    }
}
