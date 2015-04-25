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
            <?= $this->js('assets/js/app.js') ?>
        <?php endif ?>

        <?= $this->css($this->u('app', 'colors'), false, 'all') ?>
        <?= $this->css('assets/css/app.css') ?>
        <?= $this->css('assets/css/print.css', true, 'print') ?>

        <?php if ($this->config->get('application_stylesheet')): ?>
            <style><?= $this->config->get('application_stylesheet') ?></style>
        <?php endif ?>

        <link rel="icon" type="image/png" href="assets/img/favicon.png">
        <link rel="apple-touch-icon" href="assets/img/touch-icon-iphone.png">
        <link rel="apple-touch-icon" sizes="72x72" href="assets/img/touch-icon-ipad.png">
        <link rel="apple-touch-icon" sizes="114x114" href="assets/img/touch-icon-iphone-retina.png">
        <link rel="apple-touch-icon" sizes="144x144" href="assets/img/touch-icon-ipad-retina.png">

        <title><?= isset($title) ? $this->e($title) : 'Kanboard' ?></title>
    </head>
    <body data-status-url="<?= $this->u('app', 'status') ?>"
          data-login-url="<?= $this->u('auth', 'login') ?>"
          data-timezone="<?= $this->getTimezone() ?>"
          data-js-lang="<?= $this->jsLang() ?>">

    <?php if (isset($no_layout) && $no_layout): ?>
        <?= $content_for_layout ?>
    <?php else: ?>
        <header>
            <nav>
                <h1><?= $this->a('K<span>B</span>', 'app', 'index', array(), false, 'logo', t('Dashboard')).' '.$this->summary($this->e($title)) ?>
                    <?php if (! empty($description)): ?>
                        <span class="column-tooltip" title='<?= $this->e($this->markdown($description)) ?>'>
                            <i class="fa fa-info-circle"></i>
                        </span>
                    <?php endif ?>
                </h1>
                <ul>
                    <?php if (isset($board_selector) && ! empty($board_selector)): ?>
                    <li>
                        <select id="board-selector" data-notfound="<?= t('No results match:') ?>" data-placeholder="<?= t('Display another project') ?>" data-board-url="<?= $this->u('board', 'show', array('project_id' => 'PROJECT_ID')) ?>">
                            <option value=""></option>
                            <?php foreach($board_selector as $board_id => $board_name): ?>
                                <option value="<?= $board_id ?>"><?= $this->e($board_name) ?></option>
                            <?php endforeach ?>
                        </select>
                    </li>
                    <?php endif ?>
                    <li>
                        <?= $this->a(t('Logout'), 'auth', 'logout') ?>
                        <span class="username hide-tablet">(<?= $this->a($this->e($this->getFullname()), 'user', 'show', array('user_id' => $this->userSession->getId())) ?>)</span>
                    </li>
                </ul>
            </nav>
        </header>
        <section class="page">
            <?= $this->flash('<div class="alert alert-success alert-fade-out">%s</div>') ?>
            <?= $this->flashError('<div class="alert alert-error">%s</div>') ?>
            <?= $content_for_layout ?>
         </section>
     <?php endif ?>
    </body>
</html>
