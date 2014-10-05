<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="robots" content="noindex,nofollow">

        <?php if (isset($board_public_refresh_interval)): ?>
            <meta http-equiv="refresh" content="<?= $board_public_refresh_interval ?>" >
        <?php endif ?>

        <?php if (! isset($not_editable)): ?>
            <?= Helper\js('assets/js/jquery-1.11.1.min.js') ?>
            <?= Helper\js('assets/js/jquery-ui-1.10.4.custom.min.js') ?>
            <?= Helper\js('assets/js/jquery.ui.touch-punch.min.js') ?>
            <?= Helper\js('assets/js/chosen.jquery.min.js') ?>
            <?= Helper\js('assets/js/app.js') ?>
        <?php endif ?>

        <?= Helper\css('assets/css/app.css') ?>
        <?= Helper\css('assets/css/font-awesome.min.css') ?>
        <?= Helper\css('assets/css/jquery-ui-1.10.4.custom.css'); ?>
        <?= Helper\css('assets/css/chosen.min.css'); ?>

        <link rel="icon" type="image/png" href="assets/img/favicon.png">
        <link rel="apple-touch-icon" href="assets/img/touch-icon-iphone.png">
        <link rel="apple-touch-icon" sizes="72x72" href="assets/img/touch-icon-ipad.png">
        <link rel="apple-touch-icon" sizes="114x114" href="assets/img/touch-icon-iphone-retina.png">
        <link rel="apple-touch-icon" sizes="144x144" href="assets/img/touch-icon-ipad-retina.png">

        <title><?= isset($title) ? Helper\escape($title).' - Kanboard' : 'Kanboard' ?></title>
    </head>
    <body>
    <?php if (isset($no_layout) && $no_layout): ?>
        <?= $content_for_layout ?>
    <?php else: ?>
        <header>
            <nav>
                <a class="logo" href="?">kanboard</a>

                <ul>
                    <?php if (isset($board_selector) && ! empty($board_selector)): ?>
                    <li>
                        <select id="board-selector" data-placeholder="<?= t('Display another project') ?>">
                            <option value=""></option>
                            <?php foreach($board_selector as $board_id => $board_name): ?>
                                <option value="<?= $board_id ?>"><?= Helper\escape($board_name) ?></option>
                            <?php endforeach ?>
                        </select>
                    </li>
                    <?php endif ?>
                    <li <?= isset($menu) && $menu === 'boards' ? 'class="active"' : '' ?>>
                        <a href="?controller=board"><?= t('Boards') ?></a>
                    </li>
                    <li <?= isset($menu) && $menu === 'projects' ? 'class="active"' : '' ?>>
                        <a href="?controller=project"><?= t('Projects') ?></a>
                    </li>
                    <?php if (Helper\is_admin()): ?>
                        <li <?= isset($menu) && $menu === 'users' ? 'class="active"' : '' ?>>
                            <a href="?controller=user"><?= t('Users') ?></a>
                        </li>
                        <li class="hide-tablet <?= isset($menu) && $menu === 'config' ? 'active' : '' ?>">
                            <a href="?controller=config"><?= t('Settings') ?></a>
                        </li>
                    <?php endif ?>
                    <li>
                        <a href="?controller=user&amp;action=logout<?= Helper\param_csrf() ?>"><?= t('Logout') ?></a>
                        <span class="username">(<a href="?controller=user&amp;action=show&amp;user_id=<?= Helper\get_user_id() ?>"><?= Helper\escape(Helper\get_username()) ?></a>)</span>
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
