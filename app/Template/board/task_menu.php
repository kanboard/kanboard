<span class="dropdown">
    <a href="#" class="dropdown-menu"><?= '#'.$task['id'] ?> <i class="fa fa-caret-down"></i></a>
    <ul>
        <li>
            <?= $this->url->button('fa-user', t('Change assignee'), 'BoardPopover', 'changeAssignee', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->button('fa-tag', t('Change category'), 'BoardPopover', 'changeCategory', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->button('fa-align-left', t('Change description'), 'taskmodification', 'description', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->button('fa-pencil-square-o', t('Edit this task'), 'taskmodification', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->button('fa-comment-o', t('Add a comment'), 'comment', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->button('fa-code-fork', t('Add internal link'), 'TaskInternalLink', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->button('fa-external-link', t('Add external link'), 'TaskExternalLink', 'find', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->button('fa-camera', t('Add a screenshot'), 'BoardPopover', 'screenshot', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <?php if ($task['is_active'] == 1): ?>
            <li>
                <?= $this->url->button('fa-close', t('Close this task'), 'taskstatus', 'close', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
            </li>
        <?php else: ?>
            <li>
                <?= $this->url->button('fa-check-square-o', t('Open this task'), 'taskstatus', 'open', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
            </li>
        <?php endif ?>
    </ul>
</span>
