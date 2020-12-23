<div class="page-header">
    <h2><?= t('Password modification') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('UserCredentialController', 'savePassword', array('user_id' => $user['id'])) ?>">
    <?= $this->form->hidden('id', $values) ?>
    <?= $this->form->csrf() ?>

    <fieldset>
        <?= $this->form->label(t('Current password for the user "%s"', $this->user->getFullname()), 'current_password') ?>
        <?= $this->form->password('current_password', $values, $errors, array('autofocus', 'autocomplete="current-password"')) ?>

        <?= $this->form->label(t('New password for the user "%s"', $this->user->getFullname($user)), 'password') ?>
        <?= $this->form->password('password', $values, $errors, ['autocomplete="new-password"']) ?>

        <?= $this->form->label(t('Confirmation'), 'confirmation') ?>
        <?= $this->form->password('confirmation', $values, $errors) ?>
    </fieldset>

    <?= $this->modal->submitButtons() ?>
</form>
