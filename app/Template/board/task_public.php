<div class="task-board color-<?= $task['color_id'] ?> <?= $task['date_modification'] > time() - $board_highlight_period ? 'task-board-recent' : '' ?>">
    <div class="task-board-header">
        <?= $this->url->link('#'.$task['id'], 'TaskViewController', 'readonly', array('task_id' => $task['id'], 'token' => $project['token'])) ?>

        <?php if (! empty($task['owner_id'])): ?>
            <span class="task-board-assignee">
                <?= $this->text->e($task['assignee_name'] ?: $task['assignee_username']) ?>
            </span>
        <?php endif ?>

        <div class="task-board-avatars-outer">
            <div class="task-board-icons-top">
            </div>
            <div class="task-board-avatars-inner">
                <?= $this->hook->render('template:board:private:task:before-avatar', array('task' => $task)) ?>
                <?= $this->render('board/task_avatar', array('task' => $task)) ?>
                <?= $this->hook->render('template:board:private:task:after-avatar', array('task' => $task)) ?>
            </div>
        </div>
    </div>

    <?= $this->hook->render('template:board:public:task:before-title', array('task' => $task)) ?>
    <div class="task-board-title">
        <?= $this->url->link($this->text->e($task['title']), 'TaskViewController', 'readonly', array('task_id' => $task['id'], 'token' => $project['token'])) ?>
    </div>
    <?= $this->hook->render('template:board:public:task:after-title', array('task' => $task)) ?>

    <div class="task-board-icons-bottom">
        <?= $this->render('board/task_footer', array(
            'task' => $task,
            'not_editable' => $not_editable,
            'project' => $project,
        )) ?>
    </div>
</div>
