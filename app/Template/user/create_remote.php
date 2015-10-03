<section id="main">
    <div class="page-header">
        <ul>
            <li><i class="fa fa-user fa-fw"></i><?= $this->url->link(t('All users'), 'user', 'index') ?></li>
            <li><i class="fa fa-plus fa-fw"></i><?= $this->url->link(t('New local user'), 'user', 'create') ?></li>
        </ul>
    </div>
    <form method="post" action="<?= $this->url->href('user', 'save') ?>" autocomplete="off">

        <?= $this->form->csrf() ?>
        <?= $this->form->hidden('is_ldap_user', array('is_ldap_user' => 1)) ?>

        <div class="form-column">
            <?= $this->form->label(t('Username'), 'username') ?>
            <?= $this->form->text('username', $values, $errors, array('autofocus', 'required', 'maxlength="50"')) ?><br/>

            <?= $this->form->label(t('Name'), 'name') ?>
            <?= $this->form->text('name', $values, $errors) ?><br/>

            <?= $this->form->label(t('Email'), 'email') ?>
            <?= $this->form->email('email', $values, $errors) ?><br/>

            <?= $this->form->label(t('Google Id'), 'google_id') ?>
            <?= $this->form->text('google_id', $values, $errors) ?><br/>

            <?= $this->form->label(t('Github Id'), 'github_id') ?>
            <?= $this->form->text('github_id', $values, $errors) ?><br/>

            <?= $this->form->label(t('Gitlab Id'), 'gitlab_id') ?>
            <?= $this->form->text('gitlab_id', $values, $errors) ?><br/>
        </div>

        <div class="form-column">
            <?= $this->form->label(t('Add project member'), 'project_id') ?>
            <?= $this->form->select('project_id', $projects, $values, $errors) ?><br/>

            <?= $this->form->label(t('Timezone'), 'timezone') ?>
            <?= $this->form->select('timezone', $timezones, $values, $errors) ?><br/>

            <?= $this->form->label(t('Language'), 'language') ?>
            <?= $this->form->select('language', $languages, $values, $errors) ?><br/>

            <?= $this->form->checkbox('notifications_enabled', t('Enable email notifications'), 1, isset($values['notifications_enabled']) && $values['notifications_enabled'] == 1 ? true : false) ?>
            <?= $this->form->checkbox('is_admin', t('Administrator'), 1, isset($values['is_admin']) && $values['is_admin'] == 1 ? true : false) ?>
            <?= $this->form->checkbox('is_project_admin', t('Project Administrator'), 1, isset($values['is_project_admin']) && $values['is_project_admin'] == 1 ? true : false) ?>
            <?= $this->form->checkbox('disable_login_form', t('Disallow login form'), 1, isset($values['disable_login_form']) && $values['disable_login_form'] == 1) ?>
        </div>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
            <?= t('or') ?>
            <?= $this->url->link(t('cancel'), 'user', 'index') ?>
        </div>
    </form>
    <div class="alert alert-info">
        <ul>
            <li><?= t('Remote users do not store their password in Kanboard database, examples: LDAP, Google and Github accounts.') ?></li>
            <li><?= t('If you check the box "Disallow login form", credentials entered in the login form will be ignored.') ?></li>
        </ul>
    </div>
</section>