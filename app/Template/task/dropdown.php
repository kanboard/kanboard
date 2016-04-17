<div class="dropdown">
    <a href="#" class="dropdown-menu">#<?= $task['id'] ?></a>
    <ul>
        <?php if (isset($task['date_started']) && empty($task['date_started'])): ?>
        <li>
            <?= $this->url->button('fa-play', t('Set automatically the start date'), 'taskmodification', 'start', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <?php endif ?>
        <li>
            <?= $this->url->button('fa-pencil-square-o', t('Edit the task'), 'taskmodification', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->button('fa-align-left', t('Edit the description'), 'taskmodification', 'description', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->button('fa-plus', t('Add a sub-task'), 'subtask', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->button('fa-code-fork', t('Add internal link'), 'TaskInternalLink', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->button('fa-external-link', t('Add external link'), 'TaskExternalLink', 'find', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->button('fa-comment-o', t('Add a comment'), 'comment', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->button('fa-files-o', t('Duplicate'), 'taskduplication', 'duplicate', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->button('fa-clipboard', t('Duplicate to another project'), 'taskduplication', 'copy', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->button('fa-clone', t('Move to another project'), 'taskduplication', 'move', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <?php if (isset($task['is_active'])): ?>
        <li>
            <?php if ($task['is_active'] == 1): ?>
                <?= $this->url->button('fa-times', t('Close this task'), 'taskstatus', 'close', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
            <?php else: ?>
                <?= $this->url->button('fa-check-square-o', t('Open this task'), 'taskstatus', 'open', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
            <?php endif ?>
        </li>
        <?php endif ?>

        <?= $this->hook->render('template:task:dropdown') ?>
    </ul>
</div>
