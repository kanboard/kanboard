<section id="main">
    <div class="page-header">
        <h2><?= t('New user') ?></h2>
        <ul>
            <li><a href="?controller=user"><?= t('All users') ?></a></li>
        </ul>
    </div>
    <section>
    <form method="post" action="?controller=user&amp;action=save" autocomplete="off">

        <?= Helper\form_csrf() ?>

        <div class="form-column">

            <?= Helper\form_label(t('Username'), 'username') ?>
            <?= Helper\form_text('username', $values, $errors, array('autofocus', 'required')) ?><br/>

            <?= Helper\form_label(t('Name'), 'name') ?>
            <?= Helper\form_text('name', $values, $errors) ?><br/>

            <?= Helper\form_label(t('Email'), 'email') ?>
            <?= Helper\form_email('email', $values, $errors) ?><br/>

        </div>

        <div class="form-column">

            <?= Helper\form_label(t('Password'), 'password') ?>
            <?= Helper\form_password('password', $values, $errors, array('required')) ?><br/>

            <?= Helper\form_label(t('Confirmation'), 'confirmation') ?>
            <?= Helper\form_password('confirmation', $values, $errors, array('required')) ?><br/>

            <?= Helper\form_label(t('Default project'), 'default_project_id') ?>
            <?= Helper\form_select('default_project_id', $projects, $values, $errors) ?><br/>

            <?= Helper\form_checkbox('is_admin', t('Administrator'), 1, isset($values['is_admin']) && $values['is_admin'] == 1 ? true : false) ?>

        </div>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
            <?= t('or') ?> <a href="?controller=user"><?= t('cancel') ?></a>
        </div>
    </form>
    </section>
</section>