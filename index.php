<?php

use App\Core\CoreLoader;
use App\Core\MvcApplication;

define('__SEP__',           DIRECTORY_SEPARATOR);                           // stała dla domyślnego separatora plików
define('__SCANNER_DIRS__',  array('core', 'utils', 'models', 'services'));  // katalogi w /src podlegające ładowaniu

ob_start(); // włączenie buforowania
session_start(); //uruchomienie sesji serwera php

// import loadera plików
require_once __DIR__ . __SEP__ . 'src' . __SEP__ . 'core' . __SEP__ . 'CoreLoader.php';

CoreLoader::load(); // instantancja i ładowanie rdzenia aplikacji

// dodatkowe pliki konfiguracyjne
require_once __DIR__ . __SEP__ . 'src' . __SEP__ . 'config.php';

MvcApplication::run(); // instantacja aplikacji
