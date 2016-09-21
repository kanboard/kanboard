<div class="sidebar sidebar-icons">
    <div class="sidebar-title">
        <h2><?= t('Task #%d', $task['id']) ?></h2>
    </div>
    <ul>
        <li <?= $this->app->checkMenuSelection('TaskViewController', 'show') ?>>
            <?= $this->url->link('<i class="fa fa-newspaper-o fa-fw"></i>' . t('Summary'), 'TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ActivityController', 'task') ?>>
            <?= $this->url->link('<i class="fa fa-dashboard fa-fw"></i>' . t('Activity stream'), 'ActivityController', 'task', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('TaskViewController', 'transitions') ?>>
            <?= $this->url->link('<i class="fa fa-arrows-h fa-fw"></i>' . t('Transitions'), 'TaskViewController', 'transitions', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('TaskViewController', 'analytics') ?>>
            <?= $this->url->link('<i class="fa fa-bar-chart fa-fw"></i>' . t('Analytics'), 'TaskViewController', 'analytics', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <?php if ($task['time_estimated'] > 0 || $task['time_spent'] > 0): ?>
        <li <?= $this->app->checkMenuSelection('TaskViewController', 'timetracking') ?>>
            <?= $this->url->link('<i class="fa fa-clock-o fa-fw"></i>' . t('Time tracking'), 'TaskViewController', 'timetracking', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <?php endif ?>

        <?= $this->hook->render('template:task:sidebar:information', array('task' => $task)) ?>
    </ul>

    <?php if ($this->user->hasProjectAccess('TaskModificationController', 'edit', $task['project_id'])): ?>
    <div class="sidebar-title">
        <h2><?= t('Actions') ?></h2>
    </div>
    <ul>
        <li>
            <?= $this->url->link('<i class="fa fa-pencil-square-o fa-fw"></i>' . t('Edit the task'), 'TaskModificationController', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->link('<i class="fa fa-refresh fa-rotate-90 fa-fw"></i>' . t('Edit recurrence'), 'TaskRecurrenceController', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->link('<i class="fa fa-plus fa-fw"></i>' . t('Add a sub-task'), 'SubtaskController', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->link('<i class="fa fa-code-fork fa-fw"></i>' . t('Add internal link'), 'TaskInternalLinkController', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->link('<i class="fa fa-external-link fa-fw"></i>' . t('Add external link'), 'TaskExternalLinkController', 'find', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->link('<i class="fa fa-comment-o fa-fw"></i>' . t('Add a comment'), 'CommentController', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->link('<i class="fa fa-file fa-fw"></i>' . t('Attach a document'), 'TaskFileController', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->link('<i class="fa fa-camera fa-fw"></i>' . t('Add a screenshot'), 'TaskFileController', 'screenshot', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->link('<i class="fa fa-files-o fa-fw"></i>' . t('Duplicate'), 'TaskDuplicationController', 'duplicate', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->link('<i class="fa fa-clipboard fa-fw"></i>' . t('Duplicate to another project'), 'TaskDuplicationController', 'copy', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?= $this->url->link('<i class="fa fa-clone fa-fw"></i>' . t('Move to another project'), 'TaskDuplicationController', 'move', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <?php if ($task['is_active'] == 1): ?>
            <li>
                <?= $this->url->link('<i class="fa fa-arrows fa-fw"></i>' . t('Move position'), 'TaskMovePositionController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
            </li>
            <li>
                <?= $this->url->link('<i class="fa fa-times fa-fw"></i>' . t('Close this task'), 'TaskStatusController', 'close', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
            </li>
        <?php else: ?>
            <li>
                <?= $this->url->link('<i class="fa fa-check-square-o fa-fw"></i>' . t('Open this task'), 'TaskStatusController', 'open', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
            </li>
        <?php endif ?>
        <?php if ($this->user->canRemoveTask($task)): ?>
            <li>
                <?= $this->url->link('<i class="fa fa-trash-o fa-fw"></i>' . t('Remove'), 'TaskSuppressionController', 'confirm', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'redirect' => 'board'), false, 'popover') ?>
            </li>
        <?php endif ?>

        <?= $this->hook->render('template:task:sidebar:actions', array('task' => $task)) ?>
    </ul>
    <?php endif ?>
</div>
