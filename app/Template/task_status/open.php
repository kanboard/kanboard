<div class="page-header">
    <h2><?= t('Open a task') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to open this task: "%s"?', $task['title']) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'TaskStatusController',
        'open',
        array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'confirmation' => 'yes')
    ) ?>
</div>
