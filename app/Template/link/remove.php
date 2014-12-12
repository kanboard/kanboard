<div class="page-header">
    <h2><?= t('Remove a link') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this link with task ') ?>
        <strong><?= Helper\escape('#'.$link['task_inverse_id']) ?></strong>
        <?= t('?') ?>
    </p>

    <div class="form-actions">
        <?= Helper\a(t('Yes'), 'tasklist', 'remove', array('task_id' => $task['id'], 'link_id' => $link['id']), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?= Helper\a(t('cancel'), 'task', 'show', array('task_id' => $task['id'])) ?>
    </div>
</div>