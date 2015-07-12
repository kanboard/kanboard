<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="robots" content="noindex,nofollow">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <?php if (isset($board_public_refresh_interval)): ?>
            <meta http-equiv="refresh" content="<?= $board_public_refresh_interval ?>">
        <?php endif ?>

        <?php if (! isset($not_editable)): ?>
            <?= $this->asset->js('assets/js/app.js', true) ?>
        <?php endif ?>

        <?= $this->asset->colorCss() ?>
        <?= $this->asset->css('assets/css/app.css') ?>
        <?= $this->asset->css('assets/css/print.css', true, 'print') ?>
        <?= $this->asset->customCss() ?>

        <link rel="icon" type="image/png" href="<?= $this->url->dir() ?>assets/img/favicon.png">
        <link rel="apple-touch-icon" href="<?= $this->url->dir() ?>assets/img/touch-icon-iphone.png">
        <link rel="apple-touch-icon" sizes="72x72" href="<?= $this->url->dir() ?>assets/img/touch-icon-ipad.png">
        <link rel="apple-touch-icon" sizes="114x114" href="<?= $this->url->dir() ?>assets/img/touch-icon-iphone-retina.png">
        <link rel="apple-touch-icon" sizes="144x144" href="<?= $this->url->dir() ?>assets/img/touch-icon-ipad-retina.png">

        <title><?= isset($title) ? $this->e($title) : 'Kanboard' ?></title>
    </head>
    <body data-status-url="<?= $this->url->href('app', 'status') ?>"
          data-login-url="<?= $this->url->href('auth', 'login') ?>"
          data-timezone="<?= $this->app->getTimezone() ?>"
          data-js-lang="<?= $this->app->jsLang() ?>">

    <?php if (isset($no_layout) && $no_layout): ?>
        <?= $content_for_layout ?>
    <?php else: ?>
        <header>
            <nav>
                <h1><?= $this->url->link('K<span>B</span>', 'app', 'index', array(), false, 'logo', t('Dashboard')).' '.$this->e($title) ?>
                    <?php if (! empty($description)): ?>
                        <span class="tooltip" title='<?= $this->e($this->text->markdown($description)) ?>'>
                            <i class="fa fa-info-circle"></i>
                        </span>
                    <?php endif ?>
                </h1>
                <ul>
                    <?php if (isset($board_selector) && ! empty($board_selector)): ?>
                    <li>
                        <select id="board-selector" tabindex="-1" data-notfound="<?= t('No results match:') ?>" data-placeholder="<?= t('Display another project') ?>" data-board-url="<?= $this->url->href('board', 'show', array('project_id' => 'PROJECT_ID')) ?>">
                            <option value=""></option>
                            <?php foreach($board_selector as $board_id => $board_name): ?>
                                <option value="<?= $board_id ?>"><?= $this->e($board_name) ?></option>
                            <?php endforeach ?>
                        </select>
                    </li>
                    <?php endif ?>
                    <li>
                        <?= $this->url->link(t('Logout'), 'auth', 'logout') ?>
                        <span class="username hide-tablet">(<?= $this->user->getProfileLink() ?>)</span>
                    </li>
                </ul>
            </nav>
        </header>
        <section class="page">
            <?= $this->app->flashMessage() ?>
            <?= $content_for_layout ?>
         </section>
     <?php endif ?>
    </body>
</html>
