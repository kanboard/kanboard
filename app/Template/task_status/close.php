<div class="page-header">
    <h2><?= t('Close a task') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to close the task "%s" as well as all subtasks?', $task['title']) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'TaskStatusController',
        'close',
        array('task_id' => $task['id'], 'confirmation' => 'yes')
    ) ?>
</div>
