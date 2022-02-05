<div class="page-header">
    <h2><?= t('Remove a task') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this task: "%s"?', $this->text->e($task['title'])) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'TaskSuppressionController',
        'remove',
        array('task_id' => $task['id'], 'redirect' => $redirect)
    ) ?>
</div>
