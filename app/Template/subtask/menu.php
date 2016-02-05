<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog fa-fw"></i><i class="fa fa-caret-down"></i></a>
    <ul>
        <?php if ($subtask['position'] != $first_position): ?>
            <li>
                <?= $this->url->link(t('Move Up'), 'subtask', 'movePosition', array('project_id' => $task['id'], 'task_id' => $subtask['task_id'], 'subtask_id' => $subtask['id'], 'direction' => 'up'), true) ?>
            </li>
        <?php endif ?>
        <?php if ($subtask['position'] != $last_position): ?>
            <li>
                <?= $this->url->link(t('Move Down'), 'subtask', 'movePosition', array('project_id' => $task['id'], 'task_id' => $subtask['task_id'], 'subtask_id' => $subtask['id'], 'direction' => 'down'), true) ?>
            </li>
        <?php endif ?>
        <li>
            <?= $this->url->link(t('Edit'), 'subtask', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->link(t('Remove'), 'subtask', 'confirm', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id']), false, 'popover') ?>
        </li>
    </ul>
</div>
