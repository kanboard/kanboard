<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog fa-fw"></i><i class="fa fa-caret-down"></i></a>
    <ul>
        <li>
            <?= $this->url->link('<i class="fa fa-pencil-square-o" aria-hidden="true"></i>' . t('Edit'), 'SubtaskController', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->link('<i class="fa fa-trash-o" aria-hidden="true"></i>' . t('Remove'), 'SubtaskController', 'confirm', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->link('<i class="fa fa-clone" aria-hidden="true"></i>' . t('Convert to task'), 'SubtaskConverterController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id']), false, 'popover') ?>
        </li>
    </ul>
</div>
