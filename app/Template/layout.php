<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="robots" content="noindex,nofollow">

        <?php if (isset($board_public_refresh_interval)): ?>
            <meta http-equiv="refresh" content="<?= $board_public_refresh_interval ?>">
        <?php endif ?>

        <?php if (! isset($not_editable)): ?>
            <?= Helper\js('assets/js/app.js') ?>
        <?php endif ?>

        <?= Helper\css('assets/css/app.css') ?>

        <link rel="icon" type="image/png" href="assets/img/favicon.png">
        <link rel="apple-touch-icon" href="assets/img/touch-icon-iphone.png">
        <link rel="apple-touch-icon" sizes="72x72" href="assets/img/touch-icon-ipad.png">
        <link rel="apple-touch-icon" sizes="114x114" href="assets/img/touch-icon-iphone-retina.png">
        <link rel="apple-touch-icon" sizes="144x144" href="assets/img/touch-icon-ipad-retina.png">

        <title><?= isset($title) ? Helper\escape($title) : 'Kanboard' ?></title>
    </head>
    <body>
    <?php if (isset($no_layout) && $no_layout): ?>
        <?= $content_for_layout ?>
    <?php else: ?>
        <header>
            <nav>
                <h1><?= Helper\a('<i class="fa fa-home fa-fw"></i>', 'app', 'index', array(), false, 'home-link', t('Dashboard')).' '.Helper\summary(Helper\escape($title)) ?></h1>
                <ul>
                    <?php if (isset($board_selector) && ! empty($board_selector)): ?>
                    <li>
                        <select id="board-selector" data-placeholder="<?= t('Display another project') ?>" data-board-url="<?= Helper\u('board', 'show', array('project_id' => '%d')) ?>">
                            <option value=""></option>
                            <?php foreach($board_selector as $board_id => $board_name): ?>
                                <option value="<?= $board_id ?>"><?= Helper\escape($board_name) ?></option>
                            <?php endforeach ?>
                        </select>
                    </li>
                    <?php endif ?>
                    <li>
                        <?= Helper\a(t('Logout'), 'user', 'logout', array(), true) ?>
                        <span class="username">(<?= Helper\a(Helper\escape(Helper\get_username()), 'user', 'show', array('user_id' => Helper\get_user_id())) ?>)</span>
                    </li>
                </ul>
            </nav>
        </header>
        <section class="page">
            <?= Helper\flash('<div class="alert alert-success alert-fade-out">%s</div>') ?>
            <?= Helper\flash_error('<div class="alert alert-error">%s</div>') ?>
            <?= $content_for_layout ?>
         </section>
     <?php endif ?>
    </body>
</html>
