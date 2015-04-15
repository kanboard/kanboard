<div class="page-header">
    <h2><?= t('Remove hourly rate') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info"><?= t('Do you really want to remove this hourly rate?') ?></p>

    <div class="form-actions">
        <?= $this->a(t('Yes'), 'hourlyrate', 'remove', array('user_id' => $user['id'], 'rate_id' => $rate_id), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?= $this->a(t('cancel'), 'hourlyrate', 'index', array('user_id' => $user['id'])) ?>
    </div>
</div>