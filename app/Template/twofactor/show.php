<div class="page-header">
    <h2><?= t('Two factor authentication') ?></h2>
</div>

<?php if (! empty($secret) || ! empty($qrcode_url) || ! empty($key_url)): ?>
<div class="listing">
    <?php if (! empty($secret)): ?>
        <p><?= t('Secret key: ') ?><strong><?= $this->text->e($secret) ?></strong></p>
    <?php endif ?>

    <?php if (! empty($qrcode_url)): ?>
        <p><br><img src="<?= $qrcode_url ?>"/><br><br></p>
    <?php endif ?>

    <?php if (! empty($key_url)): ?>
        <p><?= t('This QR code contains the key URI: ') ?><a href="<?= $this->text->e($key_url) ?>"><?= $this->text->e($key_url) ?></a></p>
    <?php endif ?>
</div>
<?php endif ?>

<h3><?= t('Test your device') ?></h3>
<form method="post" action="<?= $this->url->href('twofactor', 'test', array('user_id' => $user['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>
    <?= $this->form->label(t('Code'), 'code') ?>
    <?= $this->form->text('code', array(), array(), array('placeholder="123456"', 'autofocus'), 'form-numeric') ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Check my code') ?></button>
    </div>
</form>