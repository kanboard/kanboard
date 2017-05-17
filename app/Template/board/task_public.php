<div class="task-board color-<?= $task['color_id'] ?> <?= $task['date_modification'] > time() - $board_highlight_period ? 'task-board-recent' : '' ?>">
    <div class="task-board-header">
        <?= $this->url->link('#'.$task['id'], 'TaskViewController', 'readonly', array('task_id' => $task['id'], 'token' => $project['token'])) ?>

        <?php // IMPORTANT: must come first to make float: right work ?>
        <div class="task-board-avatars">
            <?php 
            if (! isset($users_list)) { 
                $users_list = array();
            } // endif (!empty($users_list)) {
            ?>
            <?= $this->render('board/task_avatar', array('task' => $task, 'users_list' => $users_list)) ?>
        </div>
    </div>

    <?= $this->hook->render('template:board:public:task:before-title', array('task' => $task)) ?>
    <div class="task-board-title">
        <?= $this->url->link($this->text->e($task['title']), 'TaskViewController', 'readonly', array('task_id' => $task['id'], 'token' => $project['token'])) ?>
    </div>
    <?= $this->hook->render('template:board:public:task:after-title', array('task' => $task)) ?>

    <?= $this->render('board/task_footer', array(
        'task' => $task,
        'not_editable' => $not_editable,
        'project' => $project,
    )) ?>
</div>
