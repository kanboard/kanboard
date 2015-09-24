<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="robots" content="noindex,nofollow">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="referrer" content="no-referrer">

        <?php if (isset($board_public_refresh_interval)): ?>
            <meta http-equiv="refresh" content="<?= $board_public_refresh_interval ?>">
        <?php endif ?>

        <?php if (! isset($not_editable)): ?>
            <?= $this->asset->js('assets/js/app.js') ?>
        <?php endif ?>

        <?= $this->asset->colorCss() ?>
        <?= $this->asset->css('assets/css/app.css') ?>
        <?= $this->asset->css('assets/css/print.css', true, 'print') ?>
        <?= $this->asset->customCss() ?>

        <?= $this->hook->asset('css', 'template:layout:css') ?>
        <?= $this->hook->asset('js', 'template:layout:js') ?>

        <link rel="icon" type="image/png" href="<?= $this->url->dir() ?>assets/img/favicon.png">
        <link rel="apple-touch-icon" href="<?= $this->url->dir() ?>assets/img/touch-icon-iphone.png">
        <link rel="apple-touch-icon" sizes="72x72" href="<?= $this->url->dir() ?>assets/img/touch-icon-ipad.png">
        <link rel="apple-touch-icon" sizes="114x114" href="<?= $this->url->dir() ?>assets/img/touch-icon-iphone-retina.png">
        <link rel="apple-touch-icon" sizes="144x144" href="<?= $this->url->dir() ?>assets/img/touch-icon-ipad-retina.png">

        <title><?= isset($title) ? $this->e($title) : 'Kanboard' ?></title>

        <?= $this->hook->render('template:layout:head') ?>
    </head>
    <body data-status-url="<?= $this->url->href('app', 'status') ?>"
          data-login-url="<?= $this->url->href('auth', 'login') ?>"
          data-markdown-preview-url="<?= $this->url->href('app', 'preview') ?>"
          data-timezone="<?= $this->app->getTimezone() ?>"
          data-js-lang="<?= $this->app->jsLang() ?>">

    <?php if (isset($no_layout) && $no_layout): ?>
        <?= $content_for_layout ?>
    <?php else: ?>
        <?= $this->hook->render('template:layout:top') ?>
        <?= $this->render('header', array(
            'title' => $title,
            'description' => isset($description) ? $description : '',
            'board_selector' => isset($board_selector) ? $board_selector : array(),
        )) ?>
        <section class="page">
            <?= $this->app->flashMessage() ?>
            <?= $content_for_layout ?>
        </section>
        <?= $this->hook->render('template:layout:bottom') ?>
     <?php endif ?>
    </body>
</html>
