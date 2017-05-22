<?= $this->projectHeader->render($project, 'TaskListController', 'show') ?>

<?php if ($paginator->isEmpty()): ?>
    <p class="alert"><?= t('No tasks found.') ?></p>
<?php elseif (! $paginator->isEmpty()): ?>
    <div class="table-list">
        <?= $this->render('task_list/header', array(
            'paginator' => $paginator,
            'project'   => $project,
        )) ?>

        <?php // load a list of assignable users for task assginee dropdown
        $users_list = $this->task->getAssignableUsersList($project['id']) ?: array(); 
        ?>

        <?php foreach ($paginator->getCollection() as $task): ?>
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
<?php endif ?>
