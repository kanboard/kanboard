<div class="sidebar sidebar-icons">
    <h2><?= t('Task #%d', $task['id']) ?></h2>
    <ul>
        <li <?= $this->app->checkMenuSelection('task', 'show') ?>>
            <?= $this->url->button('fa-newspaper-o', t('Summary'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('activity', 'task') ?>>
            <?= $this->url->button('fa-dashboard', t('Activity stream'), 'activity', 'task', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('task', 'transitions') ?>>
            <?= $this->url->button('fa-arrows-h', t('Transitions'), 'task', 'transitions', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('task', 'analytics') ?>>
            <?= $this->url->button('fa-bar-chart', t('Analytics'), 'task', 'analytics', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <?php if ($task['time_estimated'] > 0 || $task['time_spent'] > 0): ?>
        <li <?= $this->app->checkMenuSelection('task', 'timetracking') ?>>
            <?= $this->url->button('fa-clock-o', t('Time tracking'), 'task', 'timetracking', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <?php endif ?>

        <?= $this->hook->render('template:task:sidebar:information', array('task' => $task)) ?>
    </ul>

    <?php if ($this->user->hasProjectAccess('taskmodification', 'edit', $task['project_id'])): ?>
    <h2><?= t('Actions') ?></h2>
    <ul>
        <li>
            <?= $this->url->button('fa-pencil-square-o', t('Edit the task'), 'taskmodification', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->button('fa-align-left', t('Edit the description'), 'taskmodification', 'description', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->button('fa-refresh fa-rotate-90', t('Edit recurrence'), 'TaskRecurrence', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
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
            <?= $this->url->button('fa-file', t('Attach a document'), 'TaskFile', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->button('fa-camera', t('Add a screenshot'), 'TaskFile', 'screenshot', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
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
        <li>
            <?php if ($task['is_active'] == 1): ?>
                <?= $this->url->button('fa-times', t('Close this task'), 'taskstatus', 'close', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
            <?php else: ?>
                <?= $this->url->button('fa-check-square-o', t('Open this task'), 'taskstatus', 'open', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
            <?php endif ?>
        </li>
        <?php if ($this->task->canRemove($task)): ?>
            <li>
                <?= $this->url->button('fa-trash-o', t('Remove'), 'task', 'remove', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
            </li>
        <?php endif ?>

        <?= $this->hook->render('template:task:sidebar:actions', array('task' => $task)) ?>
    </ul>
    <?php endif ?>
</div>
