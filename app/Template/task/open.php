<div class="page-header">
    <h2><?= t('Open a task') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to open this task: "%s"?', Helper\escape($task['title'])) ?>
    </p>

    <div class="form-actions">
        <?= Helper\a(t('Yes'), 'task', 'open', array('task_id' => $task['id'], 'confirmation' => 'yes'), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?= Helper\a(t('cancel'), 'task', 'show', array('task_id' => $task['id'])) ?>
    </div>
</div>