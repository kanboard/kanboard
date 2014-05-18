<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width">
        <meta name="mobile-web-app-capable" content="yes">

        <script src="assets/js/jquery-1.11.1.min.js"></script>
        <script src="assets/js/jquery-ui-1.10.4.custom.min.js"></script>
        <script src="assets/js/jquery.ui.touch-punch.min.js"></script>

        <link rel="stylesheet" href="assets/css/app.css" media="screen">
        <link rel="stylesheet" href="assets/css/font-awesome.min.css" media="screen">

        <link rel="icon" type="image/png" href="assets/img/favicon.png">
        <link rel="apple-touch-icon" href="assets/img/touch-icon-iphone.png">
        <link rel="apple-touch-icon" sizes="72x72" href="assets/img/touch-icon-ipad.png">
        <link rel="apple-touch-icon" sizes="114x114" href="assets/img/touch-icon-iphone-retina.png">
        <link rel="apple-touch-icon" sizes="144x144" href="assets/img/touch-icon-ipad-retina.png">

        <title><?= isset($title) ? Helper\escape($title).' - Kanboard' : 'Kanboard' ?></title>
        <?php if (isset($auto_refresh)): ?>
            <meta http-equiv="refresh" content="<?= BOARD_PUBLIC_CHECK_INTERVAL ?>" >
        <?php endif ?>
    </head>
    <body>
    <?php if (isset($no_layout)): ?>
        <?= $content_for_layout ?>
    <?php else: ?>
        <header>
            <nav>
                <a class="logo" href="?">kan<span>board</span></a>
                <ul>
                    <li <?= isset($menu) && $menu === 'boards' ? 'class="active"' : '' ?>>
                        <a href="?controller=board"><?= t('Boards') ?></a>
                    </li>
                    <li <?= isset($menu) && $menu === 'projects' ? 'class="active"' : '' ?>>
                        <a href="?controller=project"><?= t('Projects') ?></a>
                    </li>
                    <li <?= isset($menu) && $menu === 'users' ? 'class="active"' : '' ?>>
                        <a href="?controller=user"><?= t('Users') ?></a>
                    </li>
                    <li <?= isset($menu) && $menu === 'config' ? 'class="active"' : '' ?>>
                        <a href="?controller=config"><?= t('Settings') ?></a>
                    </li>
                    <li>
                        <a href="?controller=user&amp;action=logout"><?= t('Logout') ?></a>
                        (<?= Helper\escape(Helper\get_username()) ?>)
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