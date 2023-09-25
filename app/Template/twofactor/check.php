<section class="form-login">
    <div class="page-header">
        <h2><?= t('Two factor authentication') ?></h2>
    </div>
    <form method="post" action="<?= $this->url->href('TwoFactorController', 'check', array('user_id' => $this->user->getId())) ?>">
        <?= $this->form->csrf() ?>

        <?= $this->form->label(t('Code'), 'code') ?>
        <?= $this->form->text('code', array(), array(), array('placeholder="123456"', 'autofocus', 'autocomplete="one-time-code"', 'pattern="[0-9]*"', 'inputmode="numeric"'), 'form-numeric') ?>

        <div class="form-actions">
            <button type="submit" class="btn btn-blue"><?= t('Check my code') ?></button>
        </div>
    </form>
</section>
