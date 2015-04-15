<div class="page-header">
    <h2><?= t('Remove time slot') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info"><?= t('Do you really want to remove this time slot?') ?></p>

    <div class="form-actions">
        <?= $this->a(t('Yes'), 'timetableoff', 'remove', array('user_id' => $user['id'], 'slot_id' => $slot_id), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?= $this->a(t('cancel'), 'timetableoff', 'index', array('user_id' => $user['id'])) ?>
    </div>
</div>