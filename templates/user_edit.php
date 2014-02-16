<section id="main">
    <div class="page-header">
        <h2><?= t('Edit user') ?></h2>
        <ul>
            <li><a href="?controller=user"><?= t('All users') ?></a></li>
        </ul>
    </div>
    <section>
    <form method="post" action="?controller=user&amp;action=update" autocomplete="off">

        <?= Helper\form_hidden('id', $values) ?>

        <?= Helper\form_label(t('Username'), 'username') ?>
        <?= Helper\form_text('username', $values, $errors, array('required')) ?><br/>

        <?= Helper\form_label(t('Password'), 'password') ?>
        <?= Helper\form_password('password', $values, $errors) ?><br/>

        <?= Helper\form_label(t('Confirmation'), 'confirmation') ?>
        <?= Helper\form_password('confirmation', $values, $errors) ?><br/>

        <?= Helper\form_label(t('Default Project'), 'default_project_id') ?>
        <?= Helper\form_select('default_project_id', $projects, $values, $errors) ?><br/>

        <?php if ($values['is_admin'] == 1): ?>
            <?= Helper\form_checkbox('is_admin', t('Administrator'), 1, isset($values['is_admin']) && $values['is_admin'] == 1 ? true : false) ?>
        <?php endif ?>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/> <?= t('or') ?> <a href="?controller=user"><?= t('cancel') ?></a>
        </div>
    </form>
    </section>
</section>