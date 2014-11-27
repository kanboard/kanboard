<div class="page-header">
    <h2><?= t('Remove a sub-task') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this sub-task?') ?>
    </p>

    <p><strong><?= Helper\escape($subtask['title']) ?></strong></p>

    <div class="form-actions">
        <?= Helper\a(t('Yes'), 'subtask', 'remove', array('task_id' => $task['id'], 'subtask_id' => $subtask['id']), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?= Helper\a(t('cancel'), 'task', 'show', array('task_id' => $task['id'])) ?>
    </div>
</div>