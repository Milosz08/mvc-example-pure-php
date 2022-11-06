<?php

// ładowanie i import rdzenia aplikacji MVC oraz innych komponentów

require_once 'core' . DIRECTORY_SEPARATOR . 'Config.php';
require_once 'core' . DIRECTORY_SEPARATOR . 'MvcApplication.php';
require_once 'core' . DIRECTORY_SEPARATOR . 'Renderer.php';
require_once 'core' . DIRECTORY_SEPARATOR . 'DbContext.php';
require_once 'core' . DIRECTORY_SEPARATOR . 'Service.php';
require_once 'core' . DIRECTORY_SEPARATOR . 'Controller.php';

require_once 'utils' . DIRECTORY_SEPARATOR . 'Util.php';

require_once 'models' . DIRECTORY_SEPARATOR . 'BookModel.php';
require_once 'models' . DIRECTORY_SEPARATOR . 'UserModel.php';

require_once 'services' . DIRECTORY_SEPARATOR . 'AuthService.php';
require_once 'services' . DIRECTORY_SEPARATOR . 'BooksService.php';
require_once 'services' . DIRECTORY_SEPARATOR . 'UsersService.php';

require_once 'config.php';