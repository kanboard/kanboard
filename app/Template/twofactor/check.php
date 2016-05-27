<form method="post" action="<?= $this->url->href('TwoFactorController', 'check', array('user_id' => $this->user->getId())) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>
    <?= $this->form->label(t('Code'), 'code') ?>
    <?= $this->form->text('code', array(), array(), array('placeholder="123456"', 'autofocus'), 'form-numeric') ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Check my code') ?></button>
    </div>
</form>
