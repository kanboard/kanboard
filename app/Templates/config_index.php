<section id="main">

    <?php if ($user['is_admin']): ?>
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

            <div class="form-actions">
                <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
            </div>
        </form>
        </section>
    <?php endif ?>

    <div class="page-header">
        <h2><?= t('User settings') ?></h2>
    </div>
    <section class="settings">
        <ul>
            <li>
                <strong><?= t('My default project:') ?> </strong>
                <?= (isset($user['default_project_id']) && isset($projects[$user['default_project_id']])) ? Helper\escape($projects[$user['default_project_id']]) : t('None') ?>,
                <a href="?controller=user&amp;action=edit&amp;user_id=<?= $user['id'] ?>"><?= t('edit') ?></a>
            </li>
        </ul>
    </section>

    <?php if ($user['is_admin']): ?>
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
    <?php endif ?>

    <div class="page-header" id="last-logins">
        <h2><?= t('Last logins') ?></h2>
    </div>
    <?php if (! empty($last_logins)): ?>
        <table class="table-small table-hover">
        <tr>
            <th><?= t('Login date') ?></th>
            <th><?= t('Authentication method') ?></th>
            <th><?= t('IP address') ?></th>
            <th><?= t('User agent') ?></th>
        </tr>
        <?php foreach($last_logins as $login): ?>
        <tr>
            <td><?= dt('%B %e, %G at %k:%M %p', $login['date_creation']) ?></td>
            <td><?= Helper\escape($login['auth_type']) ?></td>
            <td><?= Helper\escape($login['ip']) ?></td>
            <td><?= Helper\escape($login['user_agent']) ?></td>
        </tr>
        <?php endforeach ?>
        </table>
    <?php endif ?>

    <div class="page-header" id="remember-me">
        <h2><?= t('Persistent connections') ?></h2>
    </div>
    <?php if (empty($remember_me_sessions)): ?>
        <p class="alert alert-info"><?= t('No session') ?></p>
    <?php else: ?>
        <table class="table-small table-hover">
        <tr>
            <th><?= t('Creation date') ?></th>
            <th><?= t('Expiration date') ?></th>
            <th><?= t('IP address') ?></th>
            <th><?= t('User agent') ?></th>
            <th><?= t('Action') ?></th>
        </tr>
        <?php foreach($remember_me_sessions as $session): ?>
        <tr>
            <td><?= dt('%B %e, %G at %k:%M %p', $session['date_creation']) ?></td>
            <td><?= dt('%B %e, %G at %k:%M %p', $session['expiration']) ?></td>
            <td><?= Helper\escape($session['ip']) ?></td>
            <td><?= Helper\escape($session['user_agent']) ?></td>
            <td><a href="?controller=config&amp;action=removeRememberMeToken&amp;id=<?= $session['id'].Helper\param_csrf() ?>"><?= t('Remove') ?></a></td>
        </tr>
        <?php endforeach ?>
        </table>
    <?php endif ?>
</section>
