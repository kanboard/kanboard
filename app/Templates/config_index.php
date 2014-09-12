<section id="main">

    <div class="page-header">
        <h2><?= t('Application settings') ?></h2>
    </div>
    <section>
    <form method="post" action="?controller=config&amp;action=save" autocomplete="off">

        <?= Helper\form_csrf() ?>

        <?= Helper\form_label(t('Language'), 'language') ?>
        <?= Helper\form_select('language', $languages, $values, $errors) ?><br/>

        <?= Helper\form_label(t('Timezone'), 'timezone') ?>
        <?= Helper\form_select('timezone', $timezones, $values, $errors) ?><br/>

        <?= Helper\form_label(t('Webhook URL for task creation'), 'webhooks_url_task_creation') ?>
        <?= Helper\form_text('webhooks_url_task_creation', $values, $errors) ?><br/>

        <?= Helper\form_label(t('Webhook URL for task modification'), 'webhooks_url_task_modification') ?>
        <?= Helper\form_text('webhooks_url_task_modification', $values, $errors) ?><br/>

        <?= Helper\form_label(t('Default columns for new projects (Comma-separated)'), 'default_columns') ?>
        <?= Helper\form_text('default_columns', $values, $errors) ?><br/>
        <p class="form-help"><?= t('Default values are "%s"', $default_columns) ?></p>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        </div>
    </form>
    </section>

    <div class="page-header">
        <h2><?= t('More information') ?></h2>
    </div>
    <section class="settings">
        <ul>
            <li><a href="?controller=config&amp;action=tokens<?= Helper\param_csrf() ?>"><?= t('Reset all tokens') ?></a></li>
            <li>
                <?= t('Webhooks token:') ?>
                <strong><?= Helper\escape($values['webhooks_token']) ?></strong>
            </li>
            <li>
                <?= t('API token:') ?>
                <strong><?= Helper\escape($values['api_token']) ?></strong>
            </li>
            <?php if (DB_DRIVER === 'sqlite'): ?>
                <li>
                    <?= t('Database size:') ?>
                    <strong><?= Helper\format_bytes($db_size) ?></strong>
                </li>
                <li>
                    <a href="?controller=config&amp;action=downloadDb<?= Helper\param_csrf() ?>"><?= t('Download the database') ?></a>
                    <?= t('(Gzip compressed Sqlite file)') ?>
                </li>
                <li>
                    <a href="?controller=config&amp;action=optimizeDb <?= Helper\param_csrf() ?>"><?= t('Optimize the database') ?></a>
                     <?= t('(VACUUM command)') ?>
                </li>
            <?php endif ?>
            <li>
                <?= t('Official website:') ?>
                <a href="http://kanboard.net/" target="_blank" rel="noreferer">http://kanboard.net/</a>
            </li>
            <li>
                <?= t('Application version:') ?>
                <?= APP_VERSION ?>
            </li>
        </ul>
    </section>
</section>
