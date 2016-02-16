<div class="sidebar">
    <h2><?= t('Task #%d', $task['id']) ?></h2>
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
        <li <?= $this->app->checkMenuSelection('subtask', 'show') ?>>
            <?= $this->url->link(t('Sub-tasks'), 'subtask', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('tasklink', 'show') ?>>
            <?= $this->url->link(t('Internal links'), 'tasklink', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('TaskExternalLink', 'show') ?>>
            <?= $this->url->link(t('External links'), 'TaskExternalLink', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>

        <?= $this->hook->render('template:task:sidebar', array('task' => $task)) ?>
    </ul>
</div>
