<div class="page-header">
    <h2><?= t('Edit user') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('user', 'edit', array('user_id' => $user['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('id', $values) ?>

    <?= $this->form->label(t('Username'), 'username') ?>
    <?= $this->form->text('username', $values, $errors, array('required', isset($values['is_ldap_user']) && $values['is_ldap_user'] == 1 ? 'readonly' : '', 'maxlength="50"')) ?>

    <?= $this->form->label(t('Name'), 'name') ?>
    <?= $this->form->text('name', $values, $errors) ?>

    <?= $this->form->label(t('Email'), 'email') ?>
    <?= $this->form->email('email', $values, $errors) ?>

    <?= $this->form->label(t('Timezone'), 'timezone') ?>
    <?= $this->form->select('timezone', $timezones, $values, $errors) ?>

    <?= $this->form->label(t('Language'), 'language') ?>
    <?= $this->form->select('language', $languages, $values, $errors) ?>

    <?php if ($this->user->isAdmin()): ?>
        <?= $this->form->label(t('Role'), 'role') ?>
        <?= $this->form->select('role', $roles, $values, $errors) ?>
    <?php endif ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'user', 'show', array('user_id' => $user['id'])) ?>
    </div>
</form>