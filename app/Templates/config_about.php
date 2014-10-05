<div class="page-header">
    <h2><?= t('About') ?></h2>
</div>
<section class="listing">
    <ul>
        <li>
            <?= t('Official website:') ?>
            <a href="http://kanboard.net/" target="_blank" rel="noreferer">http://kanboard.net/</a>
        </li>
        <li>
            <?= t('Application version:') ?>
            <strong><?= APP_VERSION ?></strong>
        </li>
    </ul>
</section>

<div class="page-header">
    <h2><?= t('Database') ?></h2>
</div>
<section class="listing">
    <ul>
        <li>
            <?= t('Database driver:') ?>
            <strong><?= Helper\escape(DB_DRIVER) ?></strong>
        </li>
        <?php if (DB_DRIVER === 'sqlite'): ?>
            <li>
                <?= t('Database size:') ?>
                <strong><?= Helper\format_bytes($db_size) ?></strong>
            </li>
            <li>
                <?= Helper\a(t('Download the database'), 'config', 'downloadDb', array(), true) ?>&nbsp;
                <?= t('(Gzip compressed Sqlite file)') ?>
            </li>
            <li>
                <?= Helper\a(t('Optimize the database'), 'config', 'optimizeDb', array(), true) ?>&nbsp;
                <?= t('(VACUUM command)') ?>
            </li>
        <?php endif ?>
    </ul>
</section>