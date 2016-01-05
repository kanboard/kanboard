<div class="sidebar">
    <h2><?= t('Information') ?></h2>
    <ul>
        <li <?= $this->app->checkMenuSelection('task', 'show') ?>>
            <?= $this->url->link(t('Summary'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('activity', 'task') ?>>
            <?= $this->url->link(t('Activity stream'), 'activity', 'task', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('task', 'transitions') ?>>
            <?= $this->url->link(t('Transitions'), 'task', 'transitions', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('task', 'analytics') ?>>
            <?= $this->url->link(t('Analytics'), 'task', 'analytics', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <?php if ($task['time_estimated'] > 0 || $task['time_spent'] > 0): ?>
        <li <?= $this->app->checkMenuSelection('task', 'timetracking') ?>>
            <?= $this->url->link(t('Time tracking'), 'task', 'timetracking', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <?php endif ?>

        <?= $this->hook->render('template:task:sidebar:information') ?>
    </ul>
    <?php if ($this->user->hasProjectAccess('taskmodification', 'edit', $task['project_id'])): ?>
    <h2><?= t('Actions') ?></h2>
    <ul>
        <li <?= $this->app->checkMenuSelection('taskmodification', 'edit') ?>>
            <?= $this->url->link(t('Edit the task'), 'taskmodification', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('taskmodification', 'description') ?>>
            <?= $this->url->link(t('Edit the description'), 'taskmodification', 'description', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('taskmodification', 'recurrence') ?>>
            <?= $this->url->link(t('Edit recurrence'), 'taskmodification', 'recurrence', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('subtask', 'create') ?>>
            <?= $this->url->link(t('Add a sub-task'), 'subtask', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('tasklink', 'create') ?>>
            <?= $this->url->link(t('Add a link'), 'tasklink', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('comment', 'create') ?>>
            <?= $this->url->link(t('Add a comment'), 'comment', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('file', 'create') ?>>
            <?= $this->url->link(t('Attach a document'), 'file', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('file', 'screenshot') ?>>
            <?= $this->url->link(t('Add a screenshot'), 'file', 'screenshot', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('taskduplication', 'duplicate') ?>>
            <?= $this->url->link(t('Duplicate'), 'taskduplication', 'duplicate', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('taskduplication', 'copy') ?>>
            <?= $this->url->link(t('Duplicate to another project'), 'taskduplication', 'copy', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('taskduplication', 'move') ?>>
            <?= $this->url->link(t('Move to another project'), 'taskduplication', 'move', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('taskstatus') ?>>
            <?php if ($task['is_active'] == 1): ?>
                <?= $this->url->link(t('Close this task'), 'taskstatus', 'close', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
            <?php else: ?>
                <?= $this->url->link(t('Open this task'), 'taskstatus', 'open', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
            <?php endif ?>
        </li>
        <?php if ($this->task->canRemove($task)): ?>
        <li <?= $this->app->checkMenuSelection('task', 'remove') ?>>
            <?= $this->url->link(t('Remove'), 'task', 'remove', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <?php endif ?>

        <?= $this->hook->render('template:task:sidebar:actions') ?>
    </ul>
    <?php endif ?>
    <div class="sidebar-collapse"><a href="#" title="<?= t('Hide sidebar') ?>"><i class="fa fa-chevron-left"></i></a></div>
    <div class="sidebar-expand" style="display: none"><a href="#" title="<?= t('Expand sidebar') ?>"><i class="fa fa-chevron-right"></i></a></div>
</div>
