<?php

use App\Core\Config;
use App\Utils\Util;

Util::load_env('.env');

// Plik do wprowadzania konfiguracji aplikacji w postaci wartości KLUCZ->WARTOŚĆ

Config::set('__ADMIN_ROLE', 'administrator');                                                       // rola administratora
Config::set('__USER_ROLE', 'czytelnik');                                                            // rola czytelnika (zwykły użytkownik)
Config::set('__ROLES', array(Config::get('__USER_ROLE'), Config::get('__ADMIN_ROLE')));             // tablica ról użytkowników aplikacji
Config::set('__SELF_SANITIZED', filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_URL));             // zmienna wskazująca na aktualny adres

Config::set('__MVC_DEF_METHOD', 'index');                                                           // domyślna metoda kontrolera (w przypadku braku parametrów)
Config::set('__MVC_DEF_CONTROLLER', 'auth');                                                        // domyślny kontroler (w przypadku braku parametrów)
Config::set('__MVC_CONTROLLER_SUFFIX', 'Controller');                                               // suffix używany przy klasach kontrolera
Config::set('__MVC_CONTROLLERS_DIR', Config::build_path(__DIR__, 'controllers'));                   // ścieżka do klas kontrolerów
Config::set('__MVC_VIEWS_DIR', Config::build_path(__DIR__, 'views'));                               // ścieżka do widoków aplikacji
Config::set('__MVC_VIEWS_PARTIALS_DIR', Config::build_path(__DIR__, 'views', 'partials'));          // ścieżka do widoków częściowych aplikacji
Config::set('__MVC_CONTROLLERS_NAMESPACE', 'App\Controllers\\');                                    // przestrzeń nazw dla kontrolerów

Config::set('__DB_DSN', 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME'));        // data source name do bazy danych
Config::set('__DB_USERNAME', getenv('DB_USERNAME'));                                                // nazwa użytkownika bazy danych
Config::set('__DB_PASSWORD', getenv('DB_PASSWORD'));                                                // hasło użytkownika bazy danych
Config::set('__DB_INIT_COMMANDS', array(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES "UTF8"'));         // wymuszenie kodowania znaków UTF-8

Config::set('__SHA1_SALT', getenv('SHA_SALT'));                                                     // sól do algorytmu haszującego hasła
