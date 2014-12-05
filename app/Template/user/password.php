<div class="page-header">
    <h2><?= t('Password modification') ?></h2>
</div>

<form method="post" action="<?= Helper\u('user', 'password', array('user_id' => $user['id'])) ?>" autocomplete="off">

    <?= Helper\form_hidden('id', $values) ?>
    <?= Helper\form_csrf() ?>

    <div class="alert alert-error">
        <?= Helper\form_label(t('Current password for the user "%s"', Helper\get_username()), 'current_password') ?>
        <?= Helper\form_password('current_password', $values, $errors) ?><br/>
    </div>

    <?= Helper\form_label(t('New password for the user "%s"', Helper\get_username($user)), 'password') ?>
    <?= Helper\form_password('password', $values, $errors) ?><br/>

    <?= Helper\form_label(t('Confirmation'), 'confirmation') ?>
    <?= Helper\form_password('confirmation', $values, $errors) ?><br/>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= Helper\a(t('cancel'), 'user', 'show', array('user_id' => $user['id'])) ?>
    </div>
</form>
