<?php if (! empty($task['category_id'])): ?>
<div class="task-board-category-container">
    <span class="task-board-category">
        <?php if ($not_editable): ?>
            <?= $this->text->e($task['category_name']) ?>
        <?php else: ?>
            <?= $this->url->link(
                $this->text->e($task['category_name']),
                'boardPopover',
                'changeCategory',
                array('task_id' => $task['id'], 'project_id' => $task['project_id']),
                false,
                'popover' . (! empty($task['category_description']) ? ' tooltip' : ''),
                ! empty($task['category_description']) ? $this->text->markdown($task['category_description']) : t('Change category')
            ) ?>
        <?php endif ?>
    </span>
</div>
<?php endif ?>

<div class="task-board-icons">
    <?php if ($task['score']): ?>
        <span class="task-score" title="<?= t('Complexity') ?>">
            <i class="fa fa-trophy"></i>
            <?= $this->text->e($task['score']) ?>
        </span>
    <?php endif ?>

    <?php if (! empty($task['date_due'])): ?>
        <span class="task-board-date <?= time() > $task['date_due'] ? 'task-board-date-overdue' : '' ?>">
            <i class="fa fa-calendar"></i>
            <?= $this->dt->date($task['date_due']) ?>
        </span>
    <?php endif ?>

    <?php if ($task['recurrence_status'] == \Kanboard\Model\Task::RECURRING_STATUS_PENDING): ?>
        <span title="<?= t('Recurrence') ?>" class="tooltip" data-href="<?= $this->url->href('BoardTooltip', 'recurrence', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><i class="fa fa-refresh fa-rotate-90"></i></span>
    <?php endif ?>

    <?php if ($task['recurrence_status'] == \Kanboard\Model\Task::RECURRING_STATUS_PROCESSED): ?>
        <span title="<?= t('Recurrence') ?>" class="tooltip" data-href="<?= $this->url->href('BoardTooltip', 'recurrence', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><i class="fa fa-refresh fa-rotate-90 fa-inverse"></i></span>
    <?php endif ?>

    <?php if (! empty($task['nb_links'])): ?>
        <span title="<?= t('Links') ?>" class="tooltip" data-href="<?= $this->url->href('BoardTooltip', 'tasklinks', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><i class="fa fa-code-fork fa-fw"></i><?= $task['nb_links'] ?></span>
    <?php endif ?>

    <?php if (! empty($task['nb_external_links'])): ?>
        <span title="<?= t('External links') ?>" class="tooltip" data-href="<?= $this->url->href('BoardTooltip', 'externallinks', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><i class="fa fa-external-link fa-fw"></i><?= $task['nb_external_links'] ?></span>
    <?php endif ?>

    <?php if (! empty($task['nb_subtasks'])): ?>
		<span title="<?= t('Sub-Tasks') ?>" class="tooltip" data-href="<?= $this->url->href('BoardTooltip', 'subtasks', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><i class="fa fa-bars"></i>&nbsp;<?= round($task['nb_completed_subtasks']/$task['nb_subtasks']*100, 0).'%' ?></span>
    <?php endif ?>

    <?php if (! empty($task['nb_files'])): ?>
        <span title="<?= t('Attachments') ?>" class="tooltip" data-href="<?= $this->url->href('BoardTooltip', 'attachments', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><i class="fa fa-paperclip"></i>&nbsp;<?= $task['nb_files'] ?></span>
    <?php endif ?>

    <?php if (! empty($task['nb_comments'])): ?>
        <span title="<?= $task['nb_comments'] == 1 ? t('%d comment', $task['nb_comments']) : t('%d comments', $task['nb_comments']) ?>" class="tooltip" data-href="<?= $this->url->href('BoardTooltip', 'comments', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><i class="fa fa-comment-o"></i>&nbsp;<?= $task['nb_comments'] ?></span>
    <?php endif ?>

    <?php if (! empty($task['description'])): ?>
        <span title="<?= t('Description') ?>" class="tooltip" data-href="<?= $this->url->href('BoardTooltip', 'description', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>">
            <i class="fa fa-file-text-o"></i>
        </span>
    <?php endif ?>

    <?php if (! empty($task['time_estimated'])): ?>
        <span class="task-time-estimated" title="<?= t('Time estimated') ?>"><?= $this->text->e($task['time_estimated']).'h' ?></span>
    <?php endif ?>

    <?php if ($task['is_milestone'] == 1): ?>
        <span title="<?= t('Milestone') ?>">
            <i class="fa fa-flag flag-milestone"></i>
        </span>
    <?php endif ?>
    
    <?= $this->hook->render('template:board:task:icons', array('task' => $task)) ?>

    <?= $this->task->formatPriority($project, $task) ?>

    <?php if ($task['is_active'] == 1): ?>
        <div class="task-board-age">
            <span title="<?= t('Task age in days')?>" class="task-board-age-total"><?= $this->dt->age($task['date_creation']) ?></span>
            <span title="<?= t('Days in this column')?>" class="task-board-age-column"><?= $this->dt->age($task['date_moved']) ?></span>
        </div>
    <?php else: ?>
        <span class="task-board-closed"><i class="fa fa-ban fa-fw"></i><?= t('Closed') ?></span>
    <?php endif ?>
</div>

<?= $this->hook->render('template:board:task:footer', array('task' => $task)) ?>
