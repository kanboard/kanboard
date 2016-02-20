<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog fa-fw"></i><i class="fa fa-caret-down"></i></a>
    <ul>
        <li>
            <?= $this->url->link(t('Edit'), 'subtask', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->link(t('Remove'), 'subtask', 'confirm', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id']), false, 'popover') ?>
        </li>
    </ul>
</div>
