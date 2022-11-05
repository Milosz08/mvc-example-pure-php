<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['book_operation_name'] ?> książkę | SI Lab 4</title>
    <link rel="stylesheet" href="static/styles.css">
</head> 
<body>

    <?php include \App\Core\Config::get('__MVC_VIEWS_PARTIALS_DIR') . '_header_admin.partial.html' ?>

    <main class="app__content">
        <h1><?= $data['book_operation_name'] ?> książkę</h1>
        <form method="POST" action="<?= $data['self_redirect'] ?>" class="content__form__container" novalidate>
    
            <label class="form__label" for="form__title-input">tytuł książki</label>
            <div class="input-with-text__container">
                <input
                    class="form__input" id="form__title-input" type="text" name="title"
                    value="<?= $data['form_data']['title']['value'] ?>"/>
                <div class='form__error'><?= $data['form_data']['title']['error_message'] ?></div>
            </div>

            <label class="form__label" for="form__authors-input">autorzy</label>
            <div class="input-with-text__container">
                <input
                    class="form__input" id="form__authors-input" type="text" name="authors"
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
                type="submit" name="<?= $data['op_performed'] ?>" value="<?= $data['book_operation_name'] ?> książkę"
                class="button--default button--submit button__variant--normal"/>
        </form>
    </main>

    <?php include \App\Core\Config::get('__MVC_VIEWS_PARTIALS_DIR') . '_footer.partial.html' ?>

</body>
</html>