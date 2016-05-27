<div class="page-header">
    <h2><?= t('Close a task') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to close the task "%s" as well as all subtasks?', $task['title']) ?>
    </p>

    <div class="form-actions">
        <?= $this->url->link(t('Yes'), 'TaskStatusController', 'close', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'confirmation' => 'yes'), true, 'btn btn-red popover-link') ?>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'close-popover') ?>
    </div>
</div>
