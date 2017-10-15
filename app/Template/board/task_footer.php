<?php if (! empty($task['category_id'])): ?>
<div class="task-board-category-container">
    <span class="task-board-category">
        <?php if ($not_editable): ?>
            <?= $this->text->e($task['category_name']) ?>
        <?php else: ?>
            <?= $this->url->link(
                $this->text->e($task['category_name']),
                'TaskModificationController',
                'edit',
                array('task_id' => $task['id'], 'project_id' => $task['project_id']),
                false,
                'js-modal-medium' . (! empty($task['category_description']) ? ' tooltip' : ''),
                ! empty($task['category_description']) ? $this->text->markdownAttribute($task['category_description']) : t('Change category')
            ) ?>
        <?php endif ?>
    </span>
</div>
<?php endif ?>

<?php if (! empty($task['tags'])): ?>
    <div class="task-tags">
        <ul>
        <?php foreach ($task['tags'] as $tag): ?>
            <li><?= $this->text->e($tag['name']) ?></li>
        <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>

<div class="task-board-icons">
    <div class="task-board-icons-row">
        <?php if ($task['reference']): ?>
            <span class="task-board-reference" title="<?= t('Reference') ?>">
                <?= $this->task->renderReference($task) ?>
            </span>
        <?php endif ?>
    </div>
    <div class="task-board-icons-row">
        <?php if ($task['is_milestone'] == 1): ?>
            <span title="<?= t('Milestone') ?>">
                <i class="fa fa-flag flag-milestone"></i>
            </span>
        <?php endif ?>

        <?php if ($task['score']): ?>
            <span class="task-score" title="<?= t('Complexity') ?>">
                <i class="fa fa-trophy"></i>
                <?= $this->text->e($task['score']) ?>
            </span>
        <?php endif ?>

        <?php if (! empty($task['time_estimated']) || ! empty($task['time_spent'])): ?>
            <span class="task-time-estimated" title="<?= t('Time spent and estimated') ?>">
                <?= $this->text->e($task['time_spent']) ?>/<?= $this->text->e($task['time_estimated']) ?>h
            </span>
        <?php endif ?>

        <?php if (! empty($task['date_due'])): ?>
            <span class="task-date
                <?php if (time() > $task['date_due']): ?>
                     task-date-overdue
                <?php elseif (date('Y-m-d') == date('Y-m-d', $task['date_due'])): ?>
                     task-date-today
                <?php endif ?>
                ">
                <i class="fa fa-calendar"></i>
                <?= $this->dt->datetime($task['date_due']) ?>
            </span>
        <?php endif ?>
    </div>
    <div class="task-board-icons-row">

        <?php if ($task['recurrence_status'] == \Kanboard\Model\TaskModel::RECURRING_STATUS_PENDING): ?>
            <span title="<?= t('Recurrence') ?>" class="tooltip" data-href="<?= $this->url->href('BoardTooltipController', 'recurrence', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><i class="fa fa-refresh fa-rotate-90"></i></span>
        <?php endif ?>

        <?php if ($task['recurrence_status'] == \Kanboard\Model\TaskModel::RECURRING_STATUS_PROCESSED): ?>
            <span title="<?= t('Recurrence') ?>" class="tooltip" data-href="<?= $this->url->href('BoardTooltipController', 'recurrence', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><i class="fa fa-refresh fa-rotate-90 fa-inverse"></i></span>
        <?php endif ?>

        <?php if (! empty($task['nb_links'])): ?>
            <span title="<?= t('Links') ?>" class="tooltip" data-href="<?= $this->url->href('BoardTooltipController', 'tasklinks', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><i class="fa fa-code-fork fa-fw"></i><?= $task['nb_links'] ?></span>
        <?php endif ?>

        <?php if (! empty($task['nb_external_links'])): ?>
            <span title="<?= t('External links') ?>" class="tooltip" data-href="<?= $this->url->href('BoardTooltipController', 'externallinks', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><i class="fa fa-external-link fa-fw"></i><?= $task['nb_external_links'] ?></span>
        <?php endif ?>

        <?php if (! empty($task['nb_subtasks'])): ?>
            <span title="<?= t('Sub-Tasks') ?>" class="tooltip" data-href="<?= $this->url->href('BoardTooltipController', 'subtasks', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><i class="fa fa-bars"></i>&nbsp;<?= round($task['nb_completed_subtasks']/$task['nb_subtasks']*100, 0).'%' ?></span>
        <?php endif ?>

        <?php if (! empty($task['nb_files'])): ?>
            <span title="<?= t('Attachments') ?>" class="tooltip" data-href="<?= $this->url->href('BoardTooltipController', 'attachments', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><i class="fa fa-paperclip"></i>&nbsp;<?= $task['nb_files'] ?></span>
        <?php endif ?>

        <?php if ($task['nb_comments'] > 0): ?>
            <?php if ($not_editable): ?>
                <span title="<?= $task['nb_comments'] == 1 ? t('%d comment', $task['nb_comments']) : t('%d comments', $task['nb_comments']) ?>"><i class="fa fa-comments-o"></i>&nbsp;<?= $task['nb_comments'] ?></span>
            <?php else: ?>
                <?= $this->modal->medium(
                    'comments-o',
                    $task['nb_comments'],
                    'CommentListController',
                    'show',
                    array('task_id' => $task['id'], 'project_id' => $task['project_id']),
                    $task['nb_comments'] == 1 ? t('%d comment', $task['nb_comments']) : t('%d comments', $task['nb_comments'])
                ) ?>
            <?php endif ?>
        <?php endif ?>

        <?php if (! empty($task['description'])): ?>
            <span title="<?= t('Description') ?>" class="tooltip" data-href="<?= $this->url->href('BoardTooltipController', 'description', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>">
                <i class="fa fa-file-text-o"></i>
            </span>
        <?php endif ?>

        <?php if ($task['is_active'] == 1): ?>
            <div class="task-icon-age">
                <span title="<?= t('Task age in days')?>" class="task-icon-age-total"><?= $this->dt->age($task['date_creation']) ?></span>
                <span title="<?= t('Days in this column')?>" class="task-icon-age-column"><?= $this->dt->age($task['date_moved']) ?></span>
            </div>
        <?php else: ?>
            <span class="task-board-closed"><i class="fa fa-ban fa-fw"></i><?= t('Closed') ?></span>
        <?php endif ?>

        <?= $this->task->renderPriority($task['priority']) ?>

        <?= $this->hook->render('template:board:task:icons', array('task' => $task)) ?>
    </div>
</div>

<?= $this->hook->render('template:board:task:footer', array('task' => $task)) ?>
