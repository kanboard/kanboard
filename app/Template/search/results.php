<div class="table-list">
    <?= $this->render('task_list/header', array(
        'paginator' => $paginator,
    )) ?>

    <?php foreach ($paginator->getCollection() as $task): ?>
    <?php // load a list of assignable users for task assginee dropdown
    $users_list = $this->task->getAssignableUsersList($task['project_id']) ?: array(); 
    ?>
        <div class="table-list-row color-<?= $task['color_id'] ?>">
            <?= $this->render('task_list/task_title', array(
                'task' => $task,
                'users_list' => $users_list,
            )) ?>

            <?= $this->render('task_list/task_details', array(
                'task' => $task,
            )) ?>

            <?= $this->render('task_list/task_icons', array(
                'task' => $task,
            )) ?>

            <?= $this->render('task_list/task_subtasks', array(
                'task' => $task,
            )) ?>
        </div>
    <?php endforeach ?>
</div>

<?= $paginator ?>