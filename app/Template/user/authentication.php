<div class="page-header">
    <h2><?= t('Edit Authentication') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('user', 'authentication', array('user_id' => $user['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('id', $values) ?>
    <?= $this->form->hidden('username', $values) ?>

    <?= $this->form->label(t('Google Id'), 'google_id') ?>
    <?= $this->form->text('google_id', $values, $errors) ?>

    <?= $this->form->label(t('Github Id'), 'github_id') ?>
    <?= $this->form->text('github_id', $values, $errors) ?>

    <?= $this->form->label(t('Gitlab Id'), 'gitlab_id') ?>
    <?= $this->form->text('gitlab_id', $values, $errors) ?>

    <?= $this->form->checkbox('is_ldap_user', t('Remote user'), 1, isset($values['is_ldap_user']) && $values['is_ldap_user'] == 1) ?>
    <?= $this->form->checkbox('disable_login_form', t('Disallow login form'), 1, isset($values['disable_login_form']) && $values['disable_login_form'] == 1) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'user', 'show', array('user_id' => $user['id'])) ?>
    </div>

    <div class="alert alert-info">
        <ul>
            <li><?= t('Remote users do not store their password in Kanboard database, examples: LDAP, Google and Github accounts.') ?></li>
            <li><?= t('If you check the box "Disallow login form", credentials entered in the login form will be ignored.') ?></li>
        </ul>
    </div>
</form>