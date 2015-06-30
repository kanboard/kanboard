<form method="post" action="<?= $this->url->href('twofactor', 'check', array('user_id' => $this->user->getId())) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>
    <?= $this->form->label(t('Code'), 'code') ?>
    <?= $this->form->text('code', array(), array(), array('placeholder="123456"', 'autofocus'), 'form-numeric') ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Check my code') ?>" class="btn btn-blue"/>
    </div>
</form>