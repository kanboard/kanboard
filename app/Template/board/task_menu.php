<span class="dropdown">
    <a href="#" class="dropdown-menu"><?= '#'.$task['id'] ?></a>
    <ul>
        <li><i class="fa fa-user"></i> <?= $this->url->link(t('Change assignee'), 'board', 'changeAssignee', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?></li>
        <li><i class="fa fa-tag"></i> <?= $this->url->link(t('Change category'), 'board', 'changeCategory', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?></li>
        <li><i class="fa fa-align-left"></i> <?= $this->url->link(t('Change description'), 'taskmodification', 'description', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?></li>
        <li><i class="fa fa-pencil-square-o"></i> <?= $this->url->link(t('Edit this task'), 'taskmodification', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?></li>
        <li><i class="fa fa-comment-o"></i> <?= $this->url->link(t('Add a comment'), 'comment', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?></li>
        <li><i class="fa fa-code-fork"></i> <?= $this->url->link(t('Add a link'), 'tasklink', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?></li>
        <li><i class="fa fa-camera"></i> <?= $this->url->link(t('Add a screenshot'), 'board', 'screenshot', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?></li>
        <li><i class="fa fa-close"></i> <?= $this->url->link(t('Close this task'), 'taskstatus', 'close', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'redirect' => 'board'), false, 'popover') ?></li>
    </ul>
</span>