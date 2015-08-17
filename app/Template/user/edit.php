<div class="page-header">
    <h2><?= t('Edit user') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('user', 'edit', array('user_id' => $user['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('id', $values) ?>

    <?= $this->form->label(t('Username'), 'username') ?>
    <?= $this->form->text('username', $values, $errors, array('required', $values['is_ldap_user'] == 1 ? 'readonly' : '', 'maxlength="50"')) ?><br/>

    <?= $this->form->label(t('Name'), 'name') ?>
    <?= $this->form->text('name', $values, $errors) ?><br/>

    <?= $this->form->label(t('Email'), 'email') ?>
    <?= $this->form->email('email', $values, $errors) ?><br/>

    <?= $this->form->label(t('Timezone'), 'timezone') ?>
    <?= $this->form->select('timezone', $timezones, $values, $errors) ?><br/>

    <?= $this->form->label(t('Language'), 'language') ?>
    <?= $this->form->select('language', $languages, $values, $errors) ?><br/>

    <?php if ($this->user->isAdmin()): ?>
        <?= $this->form->checkbox('is_admin', t('Administrator'), 1, isset($values['is_admin']) && $values['is_admin'] == 1) ?>
        <?= $this->form->checkbox('is_project_admin', t('Project Administrator'), 1, isset($values['is_project_admin']) && $values['is_project_admin'] == 1) ?>
    <?php endif ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'user', 'show', array('user_id' => $user['id'])) ?>
    </div>
</form>