<div class="task-list-icons">
    <?php if ($task['reference']): ?>
        <span class="task-board-reference" title="<?= t('Reference') ?>">
            <?= $this->task->renderReference($task) ?>
        </span>
    <?php endif ?>
    <?php if ($task['is_milestone'] == 1): ?>
        <span title="<?= t('Milestone') ?>">
            <em class="fa fa-flag flag-milestone"></em>
        </span>
    <?php endif ?>

    <?php if ($task['score']): ?>
        <span class="task-score" title="<?= t('Complexity') ?>">
            <em class="fa fa-trophy"></em>
        <?= $this->text->e($task['score']) ?>
        </span>
    <?php endif ?>

    <?php if (! empty($task['time_estimated']) || ! empty($task['time_spent'])): ?>
        <span class="task-time-estimated" title="<?= t('Time spent and estimated') ?>">
            <?= $this->text->e($task['time_spent']) ?>/<?= $this->text->e($task['time_estimated']) ?>h
        </span>
    <?php endif ?>

    <?php if (! empty($task['date_started'])): ?>
        <span title="<?= t('Start date') ?>" class="task-date">
            <em class="fa fa-clock-o"></em>
            <?= $this->dt->date($task['date_started']) ?>
        </span>
    <?php endif ?>

    <?php if (! empty($task['date_due'])): ?>
        <span title="<?= t('Due date') ?>" class="task-date
            <?php if (time() > $task['date_due']): ?>
                 task-date-overdue
            <?php elseif (date('Y-m-d') == date('Y-m-d', $task['date_due'])): ?>
                 task-date-today
            <?php endif ?>
            ">
            <em class="fa fa-calendar"></em>
            <?= $this->dt->datetime($task['date_due']) ?>
        </span>
    <?php endif ?>

    <?php if ($task['recurrence_status'] == \Kanboard\Model\TaskModel::RECURRING_STATUS_PENDING): ?>
        <?= $this->app->tooltipLink('<em class="fa fa-refresh fa-rotate-90"></em>', $this->url->href('BoardTooltipController', 'recurrence', array('task_id' => $task['id'], 'project_id' => $task['project_id']))) ?>
    <?php endif ?>

    <?php if ($task['recurrence_status'] == \Kanboard\Model\TaskModel::RECURRING_STATUS_PROCESSED): ?>
        <?= $this->app->tooltipLink('<em class="fa fa-refresh fa-rotate-90 fa-inverse"></em>', $this->url->href('BoardTooltipController', 'recurrence', array('task_id' => $task['id'], 'project_id' => $task['project_id']))) ?>
    <?php endif ?>

    <?php if (! empty($task['nb_links'])): ?>
        <?= $this->app->tooltipLink('<em class="fa fa-code-fork fa-fw"></em>'.$task['nb_links'], $this->url->href('BoardTooltipController', 'tasklinks', array('task_id' => $task['id'], 'project_id' => $task['project_id']))) ?>
    <?php endif ?>

    <?php if (! empty($task['nb_external_links'])): ?>
        <?= $this->app->tooltipLink('<em class="fa fa-external-link fa-fw"></em>'.$task['nb_external_links'], $this->url->href('BoardTooltipController', 'externallinks', array('task_id' => $task['id'], 'project_id' => $task['project_id']))) ?>
    <?php endif ?>

    <?php if (! empty($task['nb_subtasks'])): ?>
        <?= $this->app->tooltipLink('<em class="fa fa-bars fa-fw"></em>'.round($task['nb_completed_subtasks'] / $task['nb_subtasks'] * 100, 0).'%', $this->url->href('BoardTooltipController', 'subtasks', array('task_id' => $task['id'], 'project_id' => $task['project_id']))) ?>
    <?php endif ?>

    <?php if (! empty($task['nb_files'])): ?>
        <?= $this->app->tooltipLink('<em class="fa fa-paperclip fa-fw"></em>'.$task['nb_files'], $this->url->href('BoardTooltipController', 'attachments', array('task_id' => $task['id'], 'project_id' => $task['project_id']))) ?>
    <?php endif ?>

    <?php if ($task['nb_comments'] > 0): ?>
        <?php if ($this->user->hasProjectAccess('TaskModificationController', 'edit', $task['project_id'])): ?>
            <?= $this->modal->medium(
                'comments-o',
                $task['nb_comments'],
                'CommentListController',
                'show',
                array('task_id' => $task['id'], 'project_id' => $task['project_id']),
                $task['nb_comments'] == 1 ? t('%d comment', $task['nb_comments']) : t('%d comments', $task['nb_comments'])
            ) ?>
        <?php else: ?>
            <span title="<?= $task['nb_comments'] == 1 ? t('%d comment', $task['nb_comments']) : t('%d comments', $task['nb_comments']) ?>"><em class="fa fa-comments-o"></em>&nbsp;<?= $task['nb_comments'] ?></span>
        <?php endif ?>
    <?php endif ?>

    <?php if (! empty($task['description'])): ?>
        <?= $this->app->tooltipLink('<em class="fa fa-file-text-o"></em>', $this->url->href('BoardTooltipController', 'description', array('task_id' => $task['id'], 'project_id' => $task['project_id']))) ?>
    <?php endif ?>

    <span title="<?= t('Position') ?>">(<?= $task['position'] ?>)</span>

    <?php if ($task['is_active'] == 1): ?>
        <div class="task-icon-age">
            <span title="<?= t('Task age in days')?>" class="task-icon-age-total"><?= $this->dt->age($task['date_creation']) ?></span>
            <span title="<?= t('Days in this column')?>" class="task-icon-age-column"><?= $this->dt->age($task['date_moved']) ?></span>
        </div>
    <?php else: ?>
        <span class="task-board-closed"><em class="fa fa-ban fa-fw"></em><?= t('Closed') ?></span>
    <?php endif ?>

    <?= $this->task->renderPriority($task['priority']) ?>
</div>
