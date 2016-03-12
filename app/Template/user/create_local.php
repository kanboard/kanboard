<section id="main">
    <div class="page-header">
        <ul>
            <li><i class="fa fa-user fa-fw"></i><?= $this->url->link(t('All users'), 'user', 'index') ?></li>
            <li><i class="fa fa-plus fa-fw"></i><?= $this->url->link(t('New remote user'), 'user', 'create', array('remote' => 1)) ?></li>
        </ul>
    </div>
    <section>
    <form method="post" action="<?= $this->url->href('user', 'save') ?>" autocomplete="off">

        <?= $this->form->csrf() ?>

        <div class="form-column">
            <?= $this->form->label(t('Username'), 'username') ?>
            <?= $this->form->text('username', $values, $errors, array('autofocus', 'required', 'maxlength="50"')) ?>

            <?= $this->form->label(t('Name'), 'name') ?>
            <?= $this->form->text('name', $values, $errors) ?>

            <?= $this->form->label(t('Email'), 'email') ?>
            <?= $this->form->email('email', $values, $errors) ?>

            <?= $this->form->label(t('Password'), 'password') ?>
            <?= $this->form->password('password', $values, $errors, array('required')) ?>

            <?= $this->form->label(t('Confirmation'), 'confirmation') ?>
            <?= $this->form->password('confirmation', $values, $errors, array('required')) ?>
        </div>

        <div class="form-column">
            <?= $this->form->label(t('Add project member'), 'project_id') ?>
            <?= $this->form->select('project_id', $projects, $values, $errors) ?>

            <?= $this->form->label(t('Timezone'), 'timezone') ?>
            <?= $this->form->select('timezone', $timezones, $values, $errors) ?>

            <?= $this->form->label(t('Language'), 'language') ?>
            <?= $this->form->select('language', $languages, $values, $errors) ?>

            <?= $this->form->label(t('Role'), 'role') ?>
            <?= $this->form->select('role', $roles, $values, $errors) ?>

            <?= $this->form->checkbox('notifications_enabled', t('Enable email notifications'), 1, isset($values['notifications_enabled']) && $values['notifications_enabled'] == 1 ? true : false) ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
            <?= t('or') ?>
            <?= $this->url->link(t('cancel'), 'user', 'index') ?>
        </div>
    </form>
    </section>
</section>
