<div class="sidebar">
    <h2><?= t('Information') ?></h2>
    <ul>
        <li <?= $this->app->getRouterController() === 'task' && $this->app->getRouterAction() === 'show' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Summary'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->getRouterController() === 'activity' && $this->app->getRouterAction() === 'task' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Activity stream'), 'activity', 'task', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->getRouterController() === 'task' && $this->app->getRouterAction() === 'transitions' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Transitions'), 'task', 'transitions', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->getRouterController() === 'task' && $this->app->getRouterAction() === 'analytics' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Analytics'), 'task', 'analytics', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <?php if ($task['time_estimated'] > 0 || $task['time_spent'] > 0): ?>
        <li <?= $this->app->getRouterController() === 'task' && $this->app->getRouterAction() === 'timetracking' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Time tracking'), 'task', 'timetracking', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <?php endif ?>

        <?= $this->hook->render('template:task:sidebar:information') ?>
    </ul>
    <h2><?= t('Actions') ?></h2>
    <ul>
        <li <?= $this->app->getRouterController() === 'taskmodification' && $this->app->getRouterAction() === 'edit' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Edit the task'), 'taskmodification', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->getRouterController() === 'taskmodification' && $this->app->getRouterAction() === 'description' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Edit the description'), 'taskmodification', 'description', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->getRouterController() === 'taskmodification' && $this->app->getRouterAction() === 'recurrence' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Edit recurrence'), 'taskmodification', 'recurrence', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->getRouterController() === 'subtask' && $this->app->getRouterAction() === 'create' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Add a sub-task'), 'subtask', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->getRouterController() === 'tasklink' && $this->app->getRouterAction() === 'create' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Add a link'), 'tasklink', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->getRouterController() === 'comment' && $this->app->getRouterAction() === 'create' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Add a comment'), 'comment', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->getRouterController() === 'file' && $this->app->getRouterAction() === 'create' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Attach a document'), 'file', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->getRouterController() === 'file' && $this->app->getRouterAction() === 'screenshot' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Add a screenshot'), 'file', 'screenshot', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->getRouterController() === 'taskduplication' && $this->app->getRouterAction() === 'duplicate' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Duplicate'), 'taskduplication', 'duplicate', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->getRouterController() === 'taskduplication' && $this->app->getRouterAction() === 'copy' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Duplicate to another project'), 'taskduplication', 'copy', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->getRouterController() === 'taskduplication' && $this->app->getRouterAction() === 'move' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Move to another project'), 'taskduplication', 'move', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->getRouterController() === 'taskstatus' ? 'class="active"' : '' ?>>
            <?php if ($task['is_active'] == 1): ?>
                <?= $this->url->link(t('Close this task'), 'taskstatus', 'close', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
            <?php else: ?>
                <?= $this->url->link(t('Open this task'), 'taskstatus', 'open', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
            <?php endif ?>
        </li>
        <?php if ($this->task->canRemove($task)): ?>
        <li <?= $this->app->getRouterController() === 'task' && $this->app->getRouterAction() === 'remove' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Remove'), 'task', 'remove', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <?php endif ?>

        <?= $this->hook->render('template:task:sidebar:actions') ?>
    </ul>
    <div class="sidebar-collapse"><a href="#" title="<?= t('Hide sidebar') ?>"><i class="fa fa-chevron-left"></i></a></div>
    <div class="sidebar-expand" style="display: none"><a href="#" title="<?= t('Expand sidebar') ?>"><i class="fa fa-chevron-right"></i></a></div>
</div>
