<section id="main">
    <?= $this->projectHeader->render($project, 'TaskListController', 'show') ?>

    <?php if ($paginator->isEmpty()): ?>
        <p class="alert"><?= t('No tasks found.') ?></p>
    <?php elseif (! $paginator->isEmpty()): ?>
        <div class="task-list">
            <div class="task-list-header">
                <?= $this->render('task_list/sort_menu', array('paginator' => $paginator)) ?>
            </div>
            <?php foreach ($paginator->getCollection() as $task): ?>
                <div class="task-list-row color-<?= $task['color_id'] ?>">
                    <?= $this->render('task_list/task_title', array(
                        'task' => $task,
                    )) ?>

                    <?= $this->render('task_list/task_details', array(
                        'task' => $task,
                    )) ?>

                    <?= $this->render('task_list/task_avatars', array(
                        'task' => $task,
                    )) ?>

                    <?= $this->render('task_list/task_icons', array(
                        'project' => $project,
                        'task'    => $task,
                    )) ?>
                </div>
            <?php endforeach ?>
        </div>

        <?= $paginator ?>
    <?php endif ?>
</section>
