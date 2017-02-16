<div class="page-header">
    <h2><?= t('About') ?></h2>
</div>
<div class="panel">
    <ul>
        <li>
            <?= t('Official website:') ?>
            <a href="https://kanboard.net/" target="_blank" rel="noreferer">https://kanboard.net/</a>
        </li>
        <li>
            <?= t('Author:') ?>
            <strong>Frédéric Guillot</strong> (<a href="https://github.com/kanboard/kanboard/blob/master/CONTRIBUTORS.md" target="_blank"><?= t('contributors') ?></a>)
        </li>
        <li>
            <?= t('License:') ?>
            <strong>MIT</strong>
        </li>
    </ul>
</div>

<div class="page-header">
    <h2><?= t('Configuration') ?></h2>
</div>
<div class="panel">
    <ul>
        <li>
            <?= t('Application version:') ?>
            <strong><?= APP_VERSION ?></strong>
        </li>
        <li>
            <?= t('PHP version:') ?>
            <strong><?= PHP_VERSION ?></strong>
        </li>
        <li>
            <?= t('PHP SAPI:') ?>
            <strong><?= PHP_SAPI ?></strong>
        </li>
        <li>
            <?= t('OS version:') ?>
            <strong><?= php_uname('s').' '.php_uname('r') ?></strong>
        </li>
        <li>
            <?= t('Database driver:') ?>
            <strong><?= DB_DRIVER ?></strong>
        </li>
        <li>
            <?= t('Database version:') ?>
            <strong><?= $this->text->e($db_version) ?></strong>
        </li>
        <li>
            <?= t('Browser:') ?>
            <strong><?= $this->text->e($user_agent) ?></strong>
        </li>
    </ul>
</div>

<?php if (DB_DRIVER === 'sqlite'): ?>
    <div class="page-header">
        <h2><?= t('Database') ?></h2>
    </div>
    <div class="panel">
        <ul>
            <li>
                <?= t('Database size:') ?>
                <strong><?= $this->text->bytes($db_size) ?></strong>
            </li>
            <li>
                <?= $this->url->link(t('Download the database'), 'ConfigController', 'downloadDb', array(), true) ?>&nbsp;
                <?= t('(Gzip compressed Sqlite file)') ?>
            </li>
            <li>
                <?= $this->url->link(t('Upload the database'), 'ConfigController', 'uploadDb', array(), false, 'js-modal-medium') ?>
            </li>
            <li>
                <?= $this->url->link(t('Optimize the database'), 'ConfigController', 'optimizeDb', array(), true) ?>&nbsp;
                <?= t('(VACUUM command)') ?>
            </li>
        </ul>
    </div>
<?php endif ?>

<?= $this->render('config/keyboard_shortcuts') ?>

<div class="page-header">
    <h2><?= t('License') ?></h2>
</div>
<div class="panel">
<?= nl2br(file_get_contents(ROOT_DIR.DIRECTORY_SEPARATOR.'LICENSE')) ?>
</div>
