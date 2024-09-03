<div class="page-header">
    <h2><?= t('Edit user') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('UserModificationController', 'save', array('user_id' => $user['id'])) ?>">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('id', $values) ?>

    <fieldset>
        <legend><?= t('Profile') ?></legend>
        <?= $this->form->label(t('Username'), 'username') ?>
        <?= $this->form->text('username', $values, $errors, array('autofocus', 'required', 'autocomplete="username"', isset($user['is_ldap_user']) && $user['is_ldap_user'] == 1 ? 'readonly' : '', 'maxlength="191"')) ?>

        <?= $this->form->label(t('Name'), 'name') ?>
        <?= $this->form->text('name', $values, $errors, array($this->user->hasAccess('UserModificationController', 'show/edit_name') ? 'autocomplete="name"' : 'readonly')) ?>

        <?= $this->form->label(t('Email'), 'email') ?>
        <?= $this->form->email('email', $values, $errors, array($this->user->hasAccess('UserModificationController', 'show/edit_email') ? 'autocomplete="email"' : 'readonly')) ?>
    </fieldset>

    <fieldset>
        <legend><?= t('Preferences') ?></legend>
        <?= $this->form->label(t('Theme'), 'theme') ?>
        <?= $this->form->select('theme', $themes, $values, $errors, array($this->user->hasAccess('UserModificationController', 'show/edit_theme') ? '' : 'disabled')) ?>

        <?= $this->form->label(t('Timezone'), 'timezone') ?>
        <?= $this->form->select('timezone', $timezones, $values, $errors, array($this->user->hasAccess('UserModificationController', 'show/edit_timezone') ? '' : 'disabled')) ?>

        <?= $this->form->label(t('Language'), 'language') ?>
        <?= $this->form->select('language', $languages, $values, $errors, array($this->user->hasAccess('UserModificationController', 'show/edit_language') ? '' : 'disabled')) ?>

        <?= $this->form->label(t('Filter'), 'filter') ?>
        <?= $this->form->text('filter', $values, $errors, array($this->user->hasAccess('UserModificationController', 'show/edit_filter') ? '' : 'readonly')) ?>
    </fieldset>

    <?php if ($this->user->isAdmin()): ?>
    <fieldset>
        <legend><?= t('Security') ?></legend>
        <?= $this->form->label(t('Application role'), 'role') ?>
        <?= $this->form->select('role', $roles, $values, $errors) ?>
    </fieldset>
    <?php endif ?>

    <?= $this->modal->submitButtons() ?>
</form>
