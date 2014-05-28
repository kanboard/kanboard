<section id="main">
    <div class="page-header">
        <h2><?= t('Edit user') ?></h2>
        <ul>
            <li><a href="?controller=user"><?= t('All users') ?></a></li>
        </ul>
    </div>
    <section>
    <form method="post" action="?controller=user&amp;action=update" autocomplete="off">

        <?= Helper\form_csrf() ?>

        <div class="form-column">

            <?= Helper\form_hidden('id', $values) ?>
            <?= Helper\form_hidden('is_ldap_user', $values) ?>

            <?= Helper\form_label(t('Username'), 'username') ?>
            <?= Helper\form_text('username', $values, $errors, array('required', $values['is_ldap_user'] == 1 ? 'readonly' : '')) ?><br/>

            <?= Helper\form_label(t('Name'), 'name') ?>
            <?= Helper\form_text('name', $values, $errors) ?><br/>

            <?= Helper\form_label(t('Email'), 'email') ?>
            <?= Helper\form_email('email', $values, $errors) ?><br/>

            <?= Helper\form_label(t('Default Project'), 'default_project_id') ?>
            <?= Helper\form_select('default_project_id', $projects, $values, $errors) ?><br/>

        </div>

        <div class="form-column">

            <?php if ($values['is_ldap_user'] == 0): ?>

                <?= Helper\form_label(t('Current password for the user "%s"', Helper\get_username()), 'current_password') ?>
                <?= Helper\form_password('current_password', $values, $errors) ?><br/>

                <?= Helper\form_label(t('Password'), 'password') ?>
                <?= Helper\form_password('password', $values, $errors) ?><br/>

                <?= Helper\form_label(t('Confirmation'), 'confirmation') ?>
                <?= Helper\form_password('confirmation', $values, $errors) ?><br/>

            <?php endif ?>

            <?php if (Helper\is_admin()): ?>
                <?= Helper\form_checkbox('is_admin', t('Administrator'), 1, isset($values['is_admin']) && $values['is_admin'] == 1 ? true : false) ?><br/>
            <?php endif ?>

            <?php if (GOOGLE_AUTH && Helper\is_current_user($values['id'])): ?>
                <?php if (empty($values['google_id'])): ?>
                    <a href="?controller=user&amp;action=google<?= Helper\param_csrf() ?>"><?= t('Link my Google Account') ?></a>
                <?php else: ?>
                    <a href="?controller=user&amp;action=unlinkGoogle<?= Helper\param_csrf() ?>"><?= t('Unlink my Google Account') ?></a>
                <?php endif ?>
            <?php endif ?>

        </div>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/> <?= t('or') ?> <a href="?controller=user"><?= t('cancel') ?></a>
        </div>
    </form>
    </section>
</section>