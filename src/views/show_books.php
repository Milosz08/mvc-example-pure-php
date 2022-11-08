<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Książki | SI Lab 4</title>
    <link rel="stylesheet" href="static/styles.css">
    <script defer src="static/scripts.js"></script>
</head>
<body>
    <?php include \App\Core\Config::get('__MVC_VIEWS_PARTIALS_DIR') . '_header_' . $data['header_mode'] . '.partial.html' ?>

    <main class="app__content">
        <h1>Książki</h1>
        <div class="table-with-banner__container">
            <div id="banner-container" class="app__banner <?= $data['banner_active_class'] . ' ' . $data['banner_mode_class'] ?>">
                <?= $data['banner_text'] ?>
                <button id="close-banner-button" class="banner__close-button">x</button>
            </div>
            <?php if ($data['books_data']) { ?>
                <table class="app__table">
                    <tr>
                        <th class="cell--lp">Lp.</th>
                        <th>Tytuł</th>
                        <th>Autorzy</th>
                        <th>Liczba egzemplarzy</th>
                        <th>Akcja</th>
                    </tr>
                    <?php for ($i = 0; $i < count($data['books_data']); $i++) { ?>
                        <tr>
                            <td class="cell--lp"><?= $i + 1 ?></td>
                            <td><?= $data['books_data'][$i]->get_title() ?></td>
                            <td><?= $data['books_data'][$i]->get_authors() ?></td>
                            <td><?= $data['books_data'][$i]->get_copies() ?></td>
                            <td class="cell--center">
                                <?php if ($data['books_data'][$i]->get_copies() > 0) { ?>    
                                    <a class="button--default button__variant--normal"
                                        href="index.php?action=books/rent&_bookid=<?= $data['books_data'][$i]->get_id() ?>">
                                        Pożycz
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <div class="app__banner app__banner--inline app__banner--warn">
                    Brak zapisanych książek w bibliotece. Kliknij <a href="index.php?action=books/add">tutaj</a> aby dodać pierwszą książkę.
                </div>
            <?php } ?>
        </div>
    </main>

    <?php include \App\Core\Config::get('__MVC_VIEWS_PARTIALS_DIR') . '_footer.partial.html' ?>
</body>
</html>