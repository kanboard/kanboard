<div class="sidebar">
    <h2><?= t('Actions') ?></h2>
    <ul>
        <li>
            <?= Helper\a(t('Summary'), 'task', 'show', array('task_id' => $task['id'])) ?>
        </li>
        <li>
            <?= Helper\a(t('Edit the task'), 'task', 'edit', array('task_id' => $task['id'])) ?>
        </li>
        <li>
            <?= Helper\a(t('Edit the description'), 'task', 'description', array('task_id' => $task['id'])) ?>
        </li>
        <li>
            <?= Helper\a(t('Add a sub-task'), 'subtask', 'create', array('task_id' => $task['id'])) ?>
        </li>
        <li>
            <?= Helper\a(t('Add a comment'), 'comment', 'create', array('task_id' => $task['id'])) ?>
        </li>
        <li>
            <?= Helper\a(t('Attach a document'), 'file', 'create', array('task_id' => $task['id'])) ?>
        </li>
        <li>
            <?= Helper\a(t('Duplicate'), 'task', 'duplicate', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li>
            <?= Helper\a(t('Duplicate to another project'), 'task', 'copy', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li>
            <?= Helper\a(t('Move to another project'), 'task', 'move', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li>
            <?php if ($task['is_active'] == 1): ?>
                <?= Helper\a(t('Close this task'), 'task', 'close', array('task_id' => $task['id'])) ?>
            <?php else: ?>
                <?= Helper\a(t('Open this task'), 'task', 'open', array('task_id' => $task['id'])) ?>
            <?php endif ?>
        </li>
        <?php if (! $hide_remove_menu): ?>
        <li>
            <?= Helper\a(t('Remove'), 'task', 'remove', array('task_id' => $task['id'])) ?>
        </li>
        <?php endif ?>
    </ul>
</div>