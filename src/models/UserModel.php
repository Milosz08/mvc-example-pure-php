<?php

namespace App\Models;

// Klasa modelu dla encji "users" w bazie danych.
class UserModel
{
    protected $id; // id użytkownika
    protected $first_name; // imię
    protected $last_name; // nazwisko
    protected $login; // login
    protected $age; // wiek
    protected $role_id; // id roli
    protected $role_name; // klucz obcy do roli
    protected $full_name; // pełna nazwa użytkownika (imię i nazwisko)

    public function get_id()
    {
        return $this->id;
    }

    public function get_first_name()
    {
        return $this->first_name;
    }

    public function get_last_name()
    {
        return $this->last_name;
    }

    public function get_login()
    {
        return $this->login;
    }

    public function get_age()
    {
        return $this->age;
    }

    public function get_role_name()
    {
        return $this->role_name;
    }

    public function get_full_name()
    {
        return $this->full_name;
    }
}
