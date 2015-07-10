<div class="page-header">
    <h2><?= t('Close a task') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to close the task "%s" as well as all subtasks?', $this->e($task['title'])) ?>
    </p>

    <div class="form-actions">
        <?= $this->url->link(t('Yes'), 'task', 'close', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'confirmation' => 'yes', 'redirect' => $redirect), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?php if (in_array($redirect, array('board', 'calendar', 'listing', 'roadmap'))): ?>
            <?= $this->url->link(t('cancel'), $redirect, 'show', array('project_id' => $task['project_id'], false, 'close-popover')) ?>
        <?php else: ?>
            <?= $this->url->link(t('cancel'), 'task', 'show', array('task_id' => is_numeric($redirect) ? $redirect : $task['id'], 'project_id' => $task['project_id'])) ?>
        <?php endif ?>
    </div>
</div>