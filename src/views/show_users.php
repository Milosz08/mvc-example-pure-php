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

        <div class="table-with-banner__container">
            <div id="banner-container" class="app__banner app__banner--info <?= $data['banner_active_class'] ?>">
                <?= $data['banner_text'] ?>
                <button id="close-banner-button" class="banner__close-button">x</button>
            </div>
            <?php if ($data['users_data']) { ?>
                <table class="app__table">
                    <tr>
                        <th class="cell--lp">Lp.</th>
                        <th>Imię</th>
                        <th>Nazwisko</th>
                        <th>Login</th>
                        <th>Wiek</th>
                        <th>Uprawnienia</th>
                        <th>Akcja</th>
                    </tr>
                    <?php for ($i = 0; $i < $data['users_count']; $i++) { ?>
                        <tr>
                            <td class="cell--lp"><?= $i + 1 ?></td>
                            <td><?= $data['users_data'][$i]->get_first_name() ?></td>
                            <td><?= $data['users_data'][$i]->get_last_name() ?></td>
                            <td><?= $data['users_data'][$i]->get_login() ?></td>
                            <td><?= $data['users_data'][$i]->get_age() ?></td>
                            <td><?= $data['users_data'][$i]->get_role_name() ?></td>
                            <td class="cell--center">
                                <a class="button--default button__variant--normal"
                                    href="index.php?action=users/edit&_userid=<?= $data['users_data'][$i]->get_id() ?>">
                                    Edytuj
                                </a>
                                <a class="button--default button__variant--error button__variant--center"
                                    href="index.php?action=users/remove&_userid=<?= $data['users_data'][$i]->get_id() ?>">
                                    Usuń
                                </a>
                                <a class="button--default button__variant--normal"
                                    href="index.php?action=users/books&_userid=<?= $data['users_data'][$i]->get_id() ?>">
                                    Książki
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>
        </div>
    </main>

    <?php include \App\Core\Config::get('__MVC_VIEWS_PARTIALS_DIR') . '_footer.partial.html' ?>
</body>
</html>