<div class="page-header">
    <h2><?= t('About') ?></h2>
</div>
<div class="listing">
    <ul>
        <li>
            <?= t('Official website:') ?>
            <a href="http://kanboard.net/" target="_blank" rel="noreferer">http://kanboard.net/</a>
        </li>
        <li>
            <?= t('Application version:') ?>
            <strong><?= APP_VERSION ?></strong>
        </li>
        <li>
            <?= t('Author:') ?>
            <strong>Frédéric Guillot</strong> (<a href="https://github.com/fguillot/kanboard/blob/master/CONTRIBUTORS.md" target="_blank"><?= t('contributors') ?></a>)
        </li>
        <li>
            <?= t('License:') ?>
            <strong>MIT</strong>
        </li>
    </ul>
</div>

<div class="page-header">
    <h2><?= t('Database') ?></h2>
</div>
<div class="listing">
    <ul>
        <li>
            <?= t('Database driver:') ?>
            <strong><?= $this->e(DB_DRIVER) ?></strong>
        </li>
        <?php if (DB_DRIVER === 'sqlite'): ?>
            <li>
                <?= t('Database size:') ?>
                <strong><?= $this->text->bytes($db_size) ?></strong>
            </li>
            <li>
                <?= $this->url->link(t('Download the database'), 'config', 'downloadDb', array(), true) ?>&nbsp;
                <?= t('(Gzip compressed Sqlite file)') ?>
            </li>
            <li>
                <?= $this->url->link(t('Optimize the database'), 'config', 'optimizeDb', array(), true) ?>&nbsp;
                <?= t('(VACUUM command)') ?>
            </li>
        <?php endif ?>
    </ul>
</div>

<div class="page-header">
    <h2><?= t('Keyboard shortcuts') ?></h2>
</div>
<div class="listing">
    <h3><?= t('Board/Calendar/List view') ?></h3>
    <ul>
        <li><?= t('Switch to the board view') ?> = <strong>v b</strong></li>
        <li><?= t('Switch to the calendar view') ?> = <strong>v c</strong></li>
        <li><?= t('Switch to the list view') ?> = <strong>v l</strong></li>
        <li><?= t('Switch to the Gantt chart view') ?> = <strong>v g</strong></li>
    </ul>
    <h3><?= t('Board view') ?></h3>
    <ul>
        <li><?= t('New task') ?> = <strong>n</strong></li>
        <li><?= t('Expand/collapse tasks') ?> = <strong>s</strong></li>
        <li><?= t('Compact/wide view') ?> = <strong>c</strong></li>
    </ul>
    <h3><?= t('Application') ?></h3>
    <ul>
        <li><?= t('Open board switcher') ?> = <strong>b</strong></li>
        <li><?= t('Go to the search/filter box') ?> = <strong>f</strong></li>
        <li><?= t('Reset the search/filter box') ?> = <strong>r</strong></li>
        <li><?= t('Close dialog box') ?> = <strong>ESC</strong></li>
        <li><?= t('Submit a form') ?> = <strong>CTRL+ENTER</strong> <?= t('or') ?> <strong>⌘+ENTER</strong></li>
    </ul>
</div>

<div class="page-header">
    <h2><?= t('License') ?></h2>
</div>
<div class="listing">
<?= nl2br(file_get_contents('LICENSE')) ?>
</div>