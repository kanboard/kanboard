<div class="page-header">
    <h2><?= t('Two factor authentication') ?></h2>
</div>

<?php if ($this->app->isAjax()): ?>
    <?= $this->app->flashMessage() ?>
<?php endif ?>

<?php if (! empty($secret) || ! empty($key_url)): ?>
<div class="panel">
    <?php if (! empty($secret)): ?>
        <p><?= t('Secret key: ') ?><strong><?= $this->text->e($secret) ?></strong></p>
    <?php endif ?>

    <?php if (! empty($key_url)): ?>
        <br><img src="<?= $this->url->href('TwoFactorController', 'qrcode') ?>"><br>
        <p><?= t('This QR code contains the key URI: ') ?><a href="<?= $this->text->e($key_url) ?>"><?= $this->text->e($key_url) ?></a></p>
    <?php endif ?>
</div>
<?php endif ?>

<h3><?= t('Test your device') ?></h3>
<div class="panel">
<form method="post" action="<?= $this->url->href('TwoFactorController', 'test', array('user_id' => $user['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>
    <?= $this->form->label(t('Code'), 'code') ?>
    <?= $this->form->text('code', array(), array(), array('placeholder="123456"', 'autofocus'), 'form-numeric') ?>

    <?= $this->modal->submitButtons(array('submitLabel' => t('Check my code'))) ?>
</form>
</div>
