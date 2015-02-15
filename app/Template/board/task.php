<div class="task-board draggable-item color-<?= $task['color_id'] ?> <?= $task['date_modification'] > time() - $board_highlight_period ? 'task-board-recent' : '' ?>"
     data-task-id="<?= $task['id'] ?>"
     data-owner-id="<?= $task['owner_id'] ?>"
     data-category-id="<?= $task['category_id'] ?>"
     data-due-date="<?= $task['date_due'] ?>"
     data-task-url="<?= $this->u('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"
     title="<?= t('View this task') ?>">

    <div class="task-board-collapsed" style="display: none">
        <?= $this->a('#'.$task['id'], 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'task-board-collapsed-id') ?>
        <?= $this->a($this->e($task['title']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'task-board-collapsed-title') ?>
    </div>

    <div class="task-board-expanded">

        <ul class="dropdown">
            <li>
                <a href="#" class="dropdown-menu"><?= '#'.$task['id'] ?></a>
                <ul>
                    <li><i class="fa fa-user"></i> <?= $this->a(t('Change assignee'), 'board', 'changeAssignee', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'assignee-popover') ?></li>
                    <li><i class="fa fa-tag"></i> <?= $this->a(t('Change category'), 'board', 'changeCategory', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'category-popover') ?></li>
                    <li><i class="fa fa-align-left"></i> <?= $this->a(t('Change description'), 'task', 'description', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'task-description-popover') ?></li>
                    <li><i class="fa fa-pencil-square-o"></i> <?= $this->a(t('Edit this task'), 'task', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'task-edit-popover') ?></li>
                    <li><i class="fa fa-close"></i> <?= $this->a(t('Close this task'), 'task', 'close', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'confirmation' => 'yes', 'redirect' => 'board'), true) ?></li>
                </li>
            </li>
        </ul>

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
                'assignee-popover',
                t('Change assignee')
            ) ?>
        </span>

        <span title="<?= t('Task age in days')?>" class="task-days-age"><?= $this->getTaskAge($task['date_creation']) ?></span>
        <span title="<?= t('Days in this column')?>" class="task-days-incolumn"><?= $this->getTaskAge($task['date_moved']) ?></span>

        <div class="task-board-title">
            <?= $this->a($this->e($task['title']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, '', t('View this task')) ?>
        </div>

        <?= $this->render('board/task_footer', array('task' => $task, 'categories' => $categories)) ?>
    </div>
</div>
