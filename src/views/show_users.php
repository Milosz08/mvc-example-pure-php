<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Użytkownicy biblioteki | SI Lab 4</title>
    <link rel="stylesheet" href="static/styles.css">
</head>
<body>
    <?php include \App\Core\Config::get('__MVC_VIEWS_PARTIALS_DIR') . '_header_admin.partial.html' ?>

    <main class="app__content">
        <h1>Użytkownicy</h1>

        <a href="index.php?action=users/edit&_userid=4">Edytuj użytkownika</a>
    </main>

    <?php include \App\Core\Config::get('__MVC_VIEWS_PARTIALS_DIR') . '_footer.partial.html' ?>
</body>
</html>