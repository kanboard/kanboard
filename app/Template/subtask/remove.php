<div class="page-header">
    <h2><?= t('Remove a sub-task') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this sub-task?') ?>
    </p>

    <p><strong><?= $this->text->e($subtask['title']) ?></strong></p>

    <div class="form-actions">
        <?= $this->url->link(t('Yes'), 'subtask', 'remove', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id']), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'close-popover') ?>
    </div>
</div>