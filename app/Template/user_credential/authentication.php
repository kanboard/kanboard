<div class="page-header">
    <h2><?= t('Authentication Parameters') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('UserCredentialController', 'saveAuthentication', array('user_id' => $user['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>

    <fieldset>
        <?= $this->form->hidden('id', $values) ?>
        <?= $this->form->hidden('username', $values) ?>

        <?= $this->hook->render('template:user:authentication:form', array('values' => $values, 'errors' => $errors, 'user' => $user)) ?>

        <?= $this->form->checkbox('is_ldap_user', t('Remote user'), 1, isset($values['is_ldap_user']) && $values['is_ldap_user'] == 1) ?>
        <?= $this->form->checkbox('disable_login_form', t('Disallow login form'), 1, isset($values['disable_login_form']) && $values['disable_login_form'] == 1) ?>
    </fieldset>

    <?= $this->modal->submitButtons() ?>

    <div class="alert alert-info">
        <ul>
            <li><?= t('Remote users do not store their password in Kanboard database, examples: LDAP, Google and Github accounts.') ?></li>
            <li><?= t('If you check the box "Disallow login form", credentials entered in the login form will be ignored.') ?></li>
        </ul>
    </div>
</form>
