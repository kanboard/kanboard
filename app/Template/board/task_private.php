<div class="
        task-board
        <?= $task['is_active'] == 1 ? 'draggable-item task-board-status-open '.($task['date_modification'] > (time() - $board_highlight_period) ? 'task-board-recent' : '') : 'task-board-status-closed' ?>
        color-<?= $task['color_id'] ?>"
     data-task-id="<?= $task['id'] ?>"
     data-owner-id="<?= $task['owner_id'] ?>"
     data-category-id="<?= $task['category_id'] ?>"
     data-due-date="<?= $task['date_due'] ?>"
     data-task-url="<?= $this->url->href('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>">

    <div class="task-board-sort-handle" style="display: none;"><i class="fa fa-arrows-alt"></i></div>

    <?php if ($this->board->isCollapsed($task['project_id'])): ?>
        <div class="task-board-collapsed">
            <?= $this->render('board/task_menu', array('task' => $task)) ?>

            <?php if (! empty($task['assignee_username'])): ?>
                <span title="<?= $this->e($task['assignee_name'] ?: $task['assignee_username']) ?>">
                    <?= $this->e($this->user->getInitials($task['assignee_name'] ?: $task['assignee_username'])) ?>
                </span> -
            <?php endif ?>
            <?= $this->url->link($this->e($task['title']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'task-board-collapsed-title tooltip', $this->e($task['title'])) ?>
        </div>
    <?php else: ?>
        <div class="task-board-expanded">
            <?= $this->render('board/task_menu', array('task' => $task)) ?>

            <?php if ($task['reference']): ?>
            <span class="task-board-reference" title="<?= t('Reference') ?>">
                (<?= $task['reference'] ?>)
            </span>
            <?php endif ?>

            <?php if (! empty($task['owner_id'])): ?>
            <span class="task-board-user <?= $this->user->isCurrentUser($task['owner_id']) ? 'task-board-current-user' : '' ?>">
                <?= $this->url->link(
                    $task['assignee_name'] ?: $task['assignee_username'],
                    'board',
                    'changeAssignee',
                    array('task_id' => $task['id'], 'project_id' => $task['project_id']),
                    false,
                    'popover',
                    t('Change assignee')
                ) ?>
            </span>
            <?php endif ?>

            <?php if ($task['is_active'] == 1): ?>
            <div class="task-board-days">
                <span title="<?= t('Task age in days')?>" class="task-days-age"><?= $this->dt->age($task['date_creation']) ?></span>
                <span title="<?= t('Days in this column')?>" class="task-days-incolumn"><?= $this->dt->age($task['date_moved']) ?></span>
            </div>
            <?php else: ?>
                <div class="task-board-closed"><i class="fa fa-ban fa-fw"></i><?= t('Closed') ?></div>
            <?php endif ?>

            <div class="task-board-title">
                <?= $this->url->link($this->e($task['title']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, '', t('View this task')) ?>
            </div>

            <?= $this->render('board/task_footer', array(
                    'task' => $task,
                    'not_editable' => $not_editable,
            )) ?>
        </div>
    <?php endif ?>
</div>
