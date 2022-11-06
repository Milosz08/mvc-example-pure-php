<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie | SI Lab 4</title>
    <link rel="stylesheet" href="static/styles.css">
    <script defer src="static/scripts.js"></script>
</head>
<body>
    <?php include \App\Core\Config::get('__MVC_VIEWS_PARTIALS_DIR') . '_header_not_logged.partial.html' ?>

    <main class="app__content">
        <h1>Logowanie</h1>
        <form method="POST" action="<?= $data['self_redirect'] ?>" class="content__form__container" novalidate>
            
            <label class="form__label" for="form__login-input">Login</label>
            <div class="input-with-text__container">
                <input class="form__input" id="form__login-input" type="text" name="login" value="<?= $data['form_data']['login']['value'] ?>"/>
                <div class='form__error'><?= $data['form_data']['login']['error_message'] ?></div>
            </div>

            <label class="form__label" for="form__password-input">Hasło</label>
            <div class="input-with-text__container">
                <input class="form__input" id="form__password-input" type="password" name="password" value="<?= $data['form_data']['password']['value'] ?>"/>
                <div class='form__error'><?= $data['form_data']['password']['error_message'] ?></div>
            </div>

            <div></div>
            <input type="submit" value="Zaloguj" class="button--default button--submit button__variant--normal"/>
        </form>
    </main>

    <?php include \App\Core\Config::get('__MVC_VIEWS_PARTIALS_DIR') . '_footer.partial.html' ?>
</body>
</html>
