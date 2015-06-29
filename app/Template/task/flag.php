<div class="page-header">
    <h2>
        <?php if ($task['color_id'] == 'flagged'): ?>
            <?= t('Unflag this task') ?>
        <?php else: ?>
            <?= t('Flag this task') ?>
        <?php endif ?>
    </h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?php if ($task['color_id'] == 'flagged'): ?>
            <?= t('Do you really want to unflag this task: "%s"?', $this->e($task['title'])) ?>
        <?php else: ?>
            <?= t('Do you really want to flag this task: "%s"?', $this->e($task['title'])) ?>
        <?php endif ?>
    </p>

    <div class="form-actions">
        <?= $this->url->link(t('Yes'), 'task', 'flag', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'confirmation' => 'yes', 'redirect' => $redirect), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'close-popover') ?>
    </div>
</div>