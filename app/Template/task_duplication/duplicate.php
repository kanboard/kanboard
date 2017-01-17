<div class="page-header">
    <h2><?= t('Duplicate a task') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to duplicate this task?') ?>
    </p>

    <?= $this->modal->confirmButtons(
        'TaskDuplicationController',
        'duplicate',
        array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'confirmation' => 'yes')
    ) ?>
</div>
