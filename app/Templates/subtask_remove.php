<div class="page-header">
    <h2><?= t('Remove a sub-task') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this sub-task?') ?>
    </p>

    <p><strong><?= Helper\escape($subtask['title']) ?></strong></p>

    <div class="form-actions">
        <a href="?controller=subtask&amp;action=remove&amp;task_id=<?= $task['id'] ?>&amp;subtask_id=<?= $subtask['id'].Helper\param_csrf() ?>" class="btn btn-red"><?= t('Yes') ?></a>
        <?= t('or') ?> <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>#subtasks"><?= t('cancel') ?></a>
    </div>
</div>