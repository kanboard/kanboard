<?php
// load a list of assignable users for task assginee dropdown
$users_list = $this->task->getAssignableUsersList($project['id']) ?: array(); 
?>

<div class="
        task-board
        <?= $task['is_draggable'] ? 'draggable-item ' : '' ?>
        <?= $task['is_active'] == 1 ? 'task-board-status-open '.($task['date_modification'] > (time() - $board_highlight_period) ? 'task-board-recent' : '') : 'task-board-status-closed' ?>
        color-<?= $task['color_id'] ?>"
     data-task-id="<?= $task['id'] ?>"
     data-column-id="<?= $task['column_id'] ?>"
     data-swimlane-id="<?= $task['swimlane_id'] ?>"
     data-position="<?= $task['position'] ?>"
     data-owner-id="<?= $task['owner_id'] ?>"
     data-category-id="<?= $task['category_id'] ?>"
     data-due-date="<?= $task['date_due'] ?>"
     data-task-url="<?= $this->url->href('TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>">

    <div class="task-board-sort-handle" style="display: none;"><i class="fa fa-arrows-alt"></i></div>

    <?php if ($this->board->isCollapsed($task['project_id'])): ?>
        <div class="task-board-collapsed">
            <div class="task-board-saving-icon" style="display: none;"><i class="fa fa-spinner fa-pulse"></i></div>
            <?php // IMPORTANT: must come first to make float: right work ?>
            <div class="task-board-avatars">
                <?= $this->render('board/task_avatar', array('task' => $task, 'users_list' => $users_list)) ?>
            </div>
            
            <?php if ($this->user->hasProjectAccess('TaskModificationController', 'edit', $task['project_id'])): ?>
                <?= $this->render('task/dropdown', array('task' => $task)) ?>
            <?php else: ?>
                <strong><?= '#'.$task['id'] ?></strong>
            <?php endif ?>

            <div class="task-board-title">
            <?= $this->url->link($this->text->e($task['title']), 'TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'tooltip', $this->text->e($task['title'])) ?>
            </div>
        </div>
    <?php else: ?>
        <div class="task-board-expanded">
            <div class="task-board-saving-icon" style="display: none;"><i class="fa fa-spinner fa-pulse fa-2x"></i></div>
            
            <?php // IMPORTANT: must come first to make float: right work ?>
            <div class="task-board-avatars">
                <?= $this->render('board/task_avatar', array('task' => $task, 'users_list' => $users_list)) ?>
            </div>

            <div class="task-board-header">
                <?php if ($this->user->hasProjectAccess('TaskModificationController', 'edit', $task['project_id'])): ?>
                    <?= $this->render('task/dropdown', array('task' => $task)) ?>
                <?php else: ?>
                    <strong><?= '#'.$task['id'] ?></strong>
                <?php endif ?>

            </div>

            <?= $this->hook->render('template:board:private:task:before-title', array('task' => $task)) ?>
            <div class="task-board-title">
                <?= $this->url->link($this->text->e($task['title']), 'TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
            </div>
            <?= $this->hook->render('template:board:private:task:after-title', array('task' => $task)) ?>

            <?= $this->render('board/task_footer', array(
                'task' => $task,
                'not_editable' => $not_editable,
                'project' => $project,
            )) ?>
        </div>
    <?php endif ?>
</div>
