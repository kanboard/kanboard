<div class="page-header">
    <h2><?= t('Flag/Unflag this task') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to flag/unflag this task: "%s"?', $this->e($task['title'])) ?>
    </p>

    <div class="form-actions">
        <?= $this->url->link(t('Yes'), 'task', 'flag', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'confirmation' => 'yes', 'redirect' => $redirect), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'close-popover') ?>
    </div>
</div>