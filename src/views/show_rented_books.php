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
            <?php if ($data['rented_books_data']) { ?>
                <table class="app__table">
                    <tr>
                        <th class="cell--lp">Lp.</th>
                        <th>Tytuł</th>
                        <th>Autorzy</th>
                        <th>Liczba egzemplarzy</th>
                        <?php if ($data['is_self_user']) { ?>
                            <th>Akcja</th>
                        <?php } ?>
                    </tr>
                    <?php for ($i = 0; $i < count($data['rented_books_data']); $i++) { ?>
                        <tr>
                            <td class="cell--lp"><?= $i + 1 ?></td>
                            <td><?= $data['rented_books_data'][$i]->get_title() ?></td>
                            <td><?= $data['rented_books_data'][$i]->get_authors() ?></td>
                            <td><?= $data['rented_books_data'][$i]->get_rented_count() ?></td>
                            <?php if ($data['is_self_user']) { ?>
                                <td class="cell--center">
                                    <a class="button--default button__variant--normal"
                                        href="index.php?action=books/refund&_bookid=<?= $data['rented_books_data'][$i]->get_id() ?>">
                                        Oddaj
                                    </a>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>        
                </table>
            <?php } else { ?>
                <div class="app__banner app__banner--inline app__banner--warn">
                    Brak wypożyczonych książek. Kliknij <a href="index.php?action=books/show">tutaj</a> aby wypożyczyć książkę.
                </div>
            <?php } ?>
        </div>
    </main>

    <?php include \App\Core\Config::get('__MVC_VIEWS_PARTIALS_DIR') . '_footer.partial.html' ?>
</body>
</html>