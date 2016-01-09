<div class="form-login">
    <h2><?= t('Password Reset') ?></h2>
    <form method="post" action="<?= $this->url->href('PasswordReset', 'update', array('token' => $token)) ?>">
        <?= $this->form->csrf() ?>

        <?= $this->form->label(t('New password'), 'password') ?>
        <?= $this->form->password('password', $values, $errors) ?><br/>

        <?= $this->form->label(t('Confirmation'), 'confirmation') ?>
        <?= $this->form->password('confirmation', $values, $errors) ?>

        <div class="form-actions">
            <input type="submit" value="<?= t('Change Password') ?>" class="btn btn-blue"/>
        </div>
    </form>
</div>