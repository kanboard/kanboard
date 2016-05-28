<div class="form-login">
    <h2><?= t('Password Reset') ?></h2>
    <form method="post" action="<?= $this->url->href('PasswordResetController', 'update', array('token' => $token)) ?>">
        <?= $this->form->csrf() ?>

        <?= $this->form->label(t('New password'), 'password') ?>
        <?= $this->form->password('password', $values, $errors) ?>

        <?= $this->form->label(t('Confirmation'), 'confirmation') ?>
        <?= $this->form->password('confirmation', $values, $errors) ?>

        <div class="form-actions">
            <button type="submit" class="btn btn-blue"><?= t('Change Password') ?></button>
        </div>
    </form>
</div>
