<div class="task-board draggable-item color-<?= $task['color_id'] ?> <?= $task['date_modification'] > time() - $board_highlight_period ? 'task-board-recent' : '' ?>"
     data-task-id="<?= $task['id'] ?>"
     data-owner-id="<?= $task['owner_id'] ?>"
     data-category-id="<?= $task['category_id'] ?>"
     data-due-date="<?= $task['date_due'] ?>"
     data-task-url="<?= $this->u('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>">

    <?= $this->render('board/task_menu', array('task' => $task)) ?>

    <div class="task-board-collapsed" style="display: none">
        <?= $this->a($this->e($task['title']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'task-board-collapsed-title') ?>
    </div>

    <div class="task-board-expanded">

        <?php if ($task['reference']): ?>
        <span class="task-board-reference" title="<?= t('Reference') ?>">
            (<?= $task['reference'] ?>)
        </span>
        <?php endif ?>

        <span class="task-board-user <?= $this->userSession->isCurrentUser($task['owner_id']) ? 'task-board-current-user' : '' ?>">
            <?= $this->a(
                (! empty($task['owner_id']) ? ($task['assignee_name'] ?: $task['assignee_username']) : t('Nobody assigned')),
                'board',
                'changeAssignee',
                array('task_id' => $task['id'], 'project_id' => $task['project_id']),
                false,
                'task-board-popover',
                t('Change assignee')
            ) ?>
        </span>

        <div class="task-board-days">
            <span title="<?= t('Task age in days')?>" class="task-days-age"><?= $this->getTaskAge($task['date_creation']) ?></span>
            <span title="<?= t('Days in this column')?>" class="task-days-incolumn"><?= $this->getTaskAge($task['date_moved']) ?></span>
        </div>

        <div class="task-board-title">
            <?= $this->a($this->e($task['title']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, '', t('View this task')) ?>
        </div>

        <?= $this->render('board/task_footer', array(
                'task' => $task,
                'categories_listing' => $categories_listing,
                'categories_description' => $categories_description,
                'not_editable' => $not_editable,
        )) ?>
    </div>
</div>
