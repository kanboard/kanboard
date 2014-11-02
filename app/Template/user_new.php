<section id="main">
    <div class="page-header">
        <ul>
            <li><i class="fa fa-user fa-fw"></i><?= Helper\a(t('All users'), 'user', 'index') ?></li>
        </ul>
    </div>
    <section>
    <form method="post" action="<?= Helper\u('user', 'save') ?>" autocomplete="off">

        <?= Helper\form_csrf() ?>

        <?= Helper\form_label(t('Username'), 'username') ?>
        <?= Helper\form_text('username', $values, $errors, array('autofocus', 'required')) ?><br/>

        <?= Helper\form_label(t('Name'), 'name') ?>
        <?= Helper\form_text('name', $values, $errors) ?><br/>

        <?= Helper\form_label(t('Email'), 'email') ?>
        <?= Helper\form_email('email', $values, $errors) ?><br/>

        <?= Helper\form_label(t('Password'), 'password') ?>
        <?= Helper\form_password('password', $values, $errors, array('required')) ?><br/>

        <?= Helper\form_label(t('Confirmation'), 'confirmation') ?>
        <?= Helper\form_password('confirmation', $values, $errors, array('required')) ?><br/>

        <?= Helper\form_label(t('Default project'), 'default_project_id') ?>
        <?= Helper\form_select('default_project_id', $projects, $values, $errors) ?><br/>

        <?= Helper\form_checkbox('is_admin', t('Administrator'), 1, isset($values['is_admin']) && $values['is_admin'] == 1 ? true : false) ?>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
            <?= t('or') ?> <?= Helper\a(t('cancel'), 'user', 'index') ?>
        </div>
    </form>
    </section>
</section>