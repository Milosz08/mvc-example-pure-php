<?php

namespace App\Models;

// Klasa modelu używana w celu zalogowania użytkownika (pobieranie danych użytkownika przez PDO).
class LoginUserModel
{
    protected $id;
    protected $login;
    protected $full_name;
    protected $user_role;

    //--------------------------------------------------------------------------------------------------------------------------------------

    public function get_id()
    {
        return $this->id;
    }

    public function get_login()
    {
        return $this->login;
    }

    public function get_full_name()
    {
        return $this->full_name;
    }

    public function get_user_role()
    {
        return $this->user_role;
    }
}
