<div class="page-header">
    <h2><?= t('Two factor authentication') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('twofactor', 'save', array('user_id' => $user['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>
    <?= $this->form->checkbox('twofactor_activated', t('Enable/disable two factor authentication'), 1, isset($user['twofactor_activated']) && $user['twofactor_activated'] == 1) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>

<?php if ($user['twofactor_activated'] == 1): ?>
<div class="listing">
    <p><?= t('Secret key: ') ?><strong><?= $this->e($user['twofactor_secret']) ?></strong> (base32)</p>
    <p><br/><img src="<?= $qrcode_url ?>"/><br/><br/></p>
    <p>
        <?= t('This QR code contains the key URI: ') ?><strong><?= $this->e($key_url) ?></strong>
        <br/><br/>
        <?= t('Save the secret key in your TOTP software (by example Google Authenticator or FreeOTP).') ?>
    </p>
</div>

<h3><?= t('Test your device') ?></h3>
<form method="post" action="<?= $this->url->href('twofactor', 'test', array('user_id' => $user['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>
    <?= $this->form->label(t('Code'), 'code') ?>
    <?= $this->form->text('code', array(), array(), array('placeholder="123456"'), 'form-numeric') ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Check my code') ?>" class="btn btn-blue"/>
    </div>
</form>
<?php endif ?>
