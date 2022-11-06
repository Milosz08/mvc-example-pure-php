<?php

// ładowanie i import rdzenia aplikacji MVC oraz innych komponentów

require_once 'core/Config.php';
require_once 'core/MvcApplication.php';
require_once 'core/Renderer.php';
require_once 'core/DbContext.php';
require_once 'core/Service.php';
require_once 'core/Controller.php';

require_once 'utils/Util.php';

require_once 'models/BookModel.php';
require_once 'models/UserModel.php';

require_once 'services/AuthService.php';
require_once 'services/BooksService.php';
require_once 'services/UsersService.php';