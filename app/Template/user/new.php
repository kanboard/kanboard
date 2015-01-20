<section id="main">
    <div class="page-header">
        <ul>
            <li><i class="fa fa-user fa-fw"></i><?= $this->a(t('All users'), 'user', 'index') ?></li>
        </ul>
    </div>
    <section>
    <form method="post" action="<?= $this->u('user', 'save') ?>" autocomplete="off">

        <?= $this->formCsrf() ?>

        <?= $this->formLabel(t('Username'), 'username') ?>
        <?= $this->formText('username', $values, $errors, array('autofocus', 'required', 'maxlength="50"')) ?><br/>

        <?= $this->formLabel(t('Name'), 'name') ?>
        <?= $this->formText('name', $values, $errors) ?><br/>

        <?= $this->formLabel(t('Email'), 'email') ?>
        <?= $this->formEmail('email', $values, $errors) ?><br/>

        <?= $this->formLabel(t('Password'), 'password') ?>
        <?= $this->formPassword('password', $values, $errors, array('required')) ?><br/>

        <?= $this->formLabel(t('Confirmation'), 'confirmation') ?>
        <?= $this->formPassword('confirmation', $values, $errors, array('required')) ?><br/>

        <?= $this->formLabel(t('Default project'), 'default_project_id') ?>
        <?= $this->formSelect('default_project_id', $projects, $values, $errors) ?><br/>

        <?= $this->formLabel(t('Timezone'), 'timezone') ?>
        <?= $this->formSelect('timezone', $timezones, $values, $errors) ?><br/>

        <?= $this->formLabel(t('Language'), 'language') ?>
        <?= $this->formSelect('language', $languages, $values, $errors) ?><br/>

        <?= $this->formCheckbox('is_admin', t('Administrator'), 1, isset($values['is_admin']) && $values['is_admin'] == 1 ? true : false) ?>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
            <?= t('or') ?>
            <?= $this->a(t('cancel'), 'user', 'index') ?>
        </div>
    </form>
    </section>
</section>