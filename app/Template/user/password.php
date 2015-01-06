<div class="page-header">
    <h2><?= t('Password modification') ?></h2>
</div>

<form method="post" action="<?= $this->u('user', 'password', array('user_id' => $user['id'])) ?>" autocomplete="off">

    <?= $this->formHidden('id', $values) ?>
    <?= $this->formCsrf() ?>

    <div class="alert alert-error">
        <?= $this->formLabel(t('Current password for the user "%s"', $this->getFullname()), 'current_password') ?>
        <?= $this->formPassword('current_password', $values, $errors) ?><br/>
    </div>

    <?= $this->formLabel(t('New password for the user "%s"', $this->getFullname($user)), 'password') ?>
    <?= $this->formPassword('password', $values, $errors) ?><br/>

    <?= $this->formLabel(t('Confirmation'), 'confirmation') ?>
    <?= $this->formPassword('confirmation', $values, $errors) ?><br/>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= $this->a(t('cancel'), 'user', 'show', array('user_id' => $user['id'])) ?>
    </div>
</form>
