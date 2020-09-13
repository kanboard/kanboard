<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><div class="subtask-submenu"><em class="fa fa-cog"></em><em class="fa fa-caret-down"></em></div></a>
    <ul>
        <li>
            <?= $this->modal->medium('edit', t('Edit'), 'SubtaskController', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id'])) ?>
        </li>
        <li>
            <?= $this->modal->confirm('trash-o', t('Remove'), 'SubtaskController', 'confirm', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id'])) ?>
        </li>
        <li>
            <?= $this->modal->confirm('clone', t('Convert to task'), 'SubtaskConverterController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id'])) ?>
        </li>
    </ul>
</div>
