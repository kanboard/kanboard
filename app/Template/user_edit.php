<div class="page-header">
    <h2><?= t('Edit user') ?></h2>
</div>
<form method="post" action="?controller=user&amp;action=edit&amp;user_id=<?= $user['id'] ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>

    <?= Helper\form_hidden('id', $values) ?>
    <?= Helper\form_hidden('is_ldap_user', $values) ?>

    <?= Helper\form_label(t('Username'), 'username') ?>
    <?= Helper\form_text('username', $values, $errors, array('required', $values['is_ldap_user'] == 1 ? 'readonly' : '')) ?><br/>

    <?= Helper\form_label(t('Name'), 'name') ?>
    <?= Helper\form_text('name', $values, $errors) ?><br/>

    <?= Helper\form_label(t('Email'), 'email') ?>
    <?= Helper\form_email('email', $values, $errors) ?><br/>

    <?= Helper\form_label(t('Default project'), 'default_project_id') ?>
    <?= Helper\form_select('default_project_id', $projects, $values, $errors) ?><br/>

    <?php if (Helper\is_admin()): ?>
        <?= Helper\form_checkbox('is_admin', t('Administrator'), 1, isset($values['is_admin']) && $values['is_admin'] == 1 ? true : false) ?><br/>
    <?php endif ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/> <?= t('or') ?> <a href="?controller=user&amp;action=show&amp;user_id=<?= $user['id'] ?>"><?= t('cancel') ?></a>
    </div>
</form>