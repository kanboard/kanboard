<section id="main">
    <div class="page-header">
        <ul>
            <li><i class="fa fa-user fa-fw"></i><?= $this->url->link(t('All users'), 'user', 'index') ?></li>
        </ul>
    </div>
    <section>
    <form method="post" action="<?= $this->url->href('user', 'save') ?>" autocomplete="off">

        <?= $this->form->csrf() ?>

        <?= $this->form->label(t('Username'), 'username') ?>
        <?= $this->form->text('username', $values, $errors, array('autofocus', 'required', 'maxlength="50"')) ?><br/>

        <?= $this->form->label(t('Name'), 'name') ?>
        <?= $this->form->text('name', $values, $errors) ?><br/>

        <?= $this->form->label(t('Email'), 'email') ?>
        <?= $this->form->email('email', $values, $errors) ?><br/>

        <?= $this->form->label(t('Password'), 'password') ?>
        <?= $this->form->password('password', $values, $errors, array('required')) ?><br/>

        <?= $this->form->label(t('Confirmation'), 'confirmation') ?>
        <?= $this->form->password('confirmation', $values, $errors, array('required')) ?><br/>

        <?= $this->form->label(t('Default project'), 'default_project_id') ?>
        <?= $this->form->select('default_project_id', $projects, $values, $errors) ?><br/>

        <?= $this->form->label(t('Timezone'), 'timezone') ?>
        <?= $this->form->select('timezone', $timezones, $values, $errors) ?><br/>

        <?= $this->form->label(t('Language'), 'language') ?>
        <?= $this->form->select('language', $languages, $values, $errors) ?><br/>

        <?= $this->form->checkbox('is_admin', t('Administrator'), 1, isset($values['is_admin']) && $values['is_admin'] == 1 ? true : false) ?>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
            <?= t('or') ?>
            <?= $this->url->link(t('cancel'), 'user', 'index') ?>
        </div>
    </form>
    </section>
</section>