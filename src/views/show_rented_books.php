<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wypożyczone książki | SI Lab 4</title>
    <link rel="stylesheet" href="static/styles.css">
    <script defer src="static/scripts.js"></script>
</head>
<body>
<?php include \App\Core\Config::get('__MVC_VIEWS_PARTIALS_DIR') . '_header_' . $data['header_mode'] . '.partial.html' ?>

    <main class="app__content">
        <h1>Wypożyczone książki użytkownika <span class="user_highlight"><?= $data['user_full_name'] ?></span></h1>
        <div class="table-with-banner__container">
            <div id="banner-container" class="app__banner <?= $data['banner_active_class'] . ' ' . $data['banner_mode_class'] ?>">
                <?= $data['banner_text'] ?>
                <button id="close-banner-button" class="banner__close-button">x</button>
            </div>
            <?php var_dump($data['is_self_user']) ?>
        </div>
    </main>

    <?php include \App\Core\Config::get('__MVC_VIEWS_PARTIALS_DIR') . '_footer.partial.html' ?>
</body>
</html>