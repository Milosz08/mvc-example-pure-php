<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj książkę | SI Lab 4</title>
    <link rel="stylesheet" href="static/styles.css">
    <script defer src="static/scripts.js"></script>
</head> 
<body>
    <?php include \App\Core\Config::get('__MVC_VIEWS_PARTIALS_DIR') . '_header_admin.partial.html' ?>

    <main class="app__content">
        <h1>Dodaj książkę</h1>
        <div class="form-with-banner__container">
            <div id="banner-container" class="app__banner app__banner--error <?= $data['banner_active_class'] ?>">
                <?= $data['banner_text'] ?>
                <button id="close-banner-button" class="banner__close-button">x</button>
            </div>
            <form method="POST" action="<?= $data['self_redirect'] ?>" class="content__form__container" novalidate>
    
                <label class="form__label" for="form__title-input">tytuł książki</label>
                <div class="input-with-text__container">
                    <input
                        class="form__input" id="form__title-input" type="text" name="title" maxlength="30"
                        value="<?= $data['form_data']['title']['value'] ?>"/>
                    <div class='form__error'><?= $data['form_data']['title']['error_message'] ?></div>
                </div>

                <label class="form__label" for="form__authors-input">autorzy</label>
                <div class="input-with-text__container">
                    <input
                        class="form__input" id="form__authors-input" type="text" name="authors" maxlength="100"
                        value="<?= $data['form_data']['authors']['value'] ?>"/>
                    <div class='form__error'><?= $data['form_data']['authors']['error_message'] ?></div>
                </div>
        
                <label class="form__label" for="form__copies-input">liczba egzemplarzy</label>
                <div class="input-with-text__container">
                    <input
                        class="form__input" id="form__copies-input" type="number" name="copies" min="1" max="10000"
                        value="<?= $data['form_data']['copies']['value'] ?>"/>
                    <div class='form__error'><?= $data['form_data']['copies']['error_message'] ?></div>
                </div>

                <div></div>
                <input
                    type="submit" name="<?= $data['op_performed'] ?>" value="Dodaj książkę"
                    class="button--default button--submit button__variant--normal"/>
            </form>
        </div>
    </main>

    <?php include \App\Core\Config::get('__MVC_VIEWS_PARTIALS_DIR') . '_footer.partial.html' ?>
</body>
</html>