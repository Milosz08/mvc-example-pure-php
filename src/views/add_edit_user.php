<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['user_operation_name'] ?> użytkownika | SI Lab 4</title>
    <link rel="stylesheet" href="static/styles.css">
</head> 
<body>

    <?php include \App\Core\Config::get('__MVC_VIEWS_PARTIALS_DIR') . '_header_admin.partial.html' ?>

    <main class="app__content">
        <h1><?= $data['user_operation_name'] ?> użytkownika</h1>
        <form method="POST" action="<?= $data['self_redirect'] ?>" class="content__form__container" novalidate>

            <label class="form__label" for="form__first-name-input">imię</label>
            <div class="input-with-text__container">
                <input
                    class="form__input" id="form__first-name-input" type="text" name="first_name"
                    value="<?= $data['form_data']['first_name']['value'] ?>"/>
                <div class='form__error'><?= $data['form_data']['first_name']['error_message'] ?></div>
            </div>

            <label class="form__label" for="form__last-name-input">nazwisko</label>
            <div class="input-with-text__container">
                <input
                    class="form__input" id="form__last-name-input" type="text" name="last_name"
                    value="<?= $data['form_data']['last_name']['value'] ?>"/>
                <div class='form__error'><?= $data['form_data']['last_name']['error_message'] ?></div>
            </div>

            <label class="form__label" for="form__login-input">login</label>
            <div class="input-with-text__container">
                <input
                    class="form__input" id="form__login-input" type="text" name="login"
                    value="<?= $data['form_data']['login']['value'] ?>"/>
                <div class='form__error'><?= $data['form_data']['login']['error_message'] ?></div>
            </div>

            <label class="form__label" for="form__age-input">wiek</label>
            <div class="input-with-text__container">
                <input
                    class="form__input" id="form__age-input" type="number" name="age" min="10" max="100"
                    value="<?= $data['form_data']['age']['value'] ?>"/>
                <div class='form__error'><?= $data['form_data']['age']['error_message'] ?></div>
            </div>

            <label class="form__label" for="form__permission-select">uprawnienia</label>
            <div class="input-with-text__container">
                <select
                    class="form__input" id="form__permission-select" name="permission"
                    value="<?= $data['form_data']['permission']['value'] ?>">
                    <?= $data['permissions'] ?>
                </select>
                <div class='form__error'><?= $data['form_data']['permission']['error_message'] ?></div>
            </div>

            <div></div>
            <input
                type="submit" name="<?= $data['op_performed'] ?>" value="<?= $data['user_operation_name'] ?> użytkownika"
                class="button--default button--submit button__variant--normal"/>
        </form>
    </main>

    <?php include \App\Core\Config::get('__MVC_VIEWS_PARTIALS_DIR') . '_footer.partial.html' ?>

</body>
</html>