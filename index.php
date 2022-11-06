<?php

use App\Core\MvcApplication;

ob_start(); // włączenie buforowania
session_start(); //uruchomienie sesji serwera php

// import i stworzenie głównej instancji aplikacji MVC
require_once __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'bootstrapper.php';

//------------------------------------------------------------------------------------------------------------------------------------------

MvcApplication::run(); // instantacja aplikacji