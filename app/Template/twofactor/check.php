<form method="post" action="<?= $this->u('twofactor', 'check', array('user_id' => $this->userSession->getId())) ?>" autocomplete="off">

    <?= $this->formCsrf() ?>
    <?= $this->formLabel(t('Code'), 'code') ?>
    <?= $this->formText('code', array(), array(), array('placeholder="123456"'), 'form-numeric') ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Check my code') ?>" class="btn btn-blue"/>
    </div>
</form>