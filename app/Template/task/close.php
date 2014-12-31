<div class="page-header">
    <h2><?= t('Close a task') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to close this task: "%s"?', $this->e($task['title'])) ?>
    </p>

    <div class="form-actions">
        <?= $this->a(t('Yes'), 'task', 'close', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'confirmation' => 'yes'), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?= $this->a(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
    </div>
</div>