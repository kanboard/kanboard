<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><div class="subtask-submenu"><i class="fa fa-cog"></i><i class="fa fa-caret-down"></i></div></a>
    <ul>
        <li>
            <?= $this->modal->medium('edit', t('Edit'), 'SubtaskController', 'edit', array('task_id' => $task['id'], 'subtask_id' => $subtask['id'])) ?>
        </li>
        <li>
            <?= $this->modal->confirm('trash-o', t('Remove'), 'SubtaskController', 'confirm', array('task_id' => $task['id'], 'subtask_id' => $subtask['id'])) ?>
        </li>
        <?php if ($this->projectRole->canCreateTaskInColumn($task['project_id'], $task['column_id'])): ?>
        <li>
            <?= $this->modal->confirm('clone', t('Convert to task'), 'SubtaskConverterController', 'show', array('task_id' => $task['id'], 'subtask_id' => $subtask['id'])) ?>
        </li>
        <?php endif ?>
    </ul>
</div>
