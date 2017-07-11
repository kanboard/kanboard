<div class="page-header">
    <h2><?= $this->url->link(t('My tasks'), 'DashboardController', 'tasks', array('user_id' => $user['id'])) ?> (<?= $paginator->getTotal() ?>)</h2>
</div>
<?php if ($paginator->isEmpty()): ?>
    <p class="alert"><?= t('There is nothing assigned to you.') ?></p>
<?php else: ?>
    <div class="table-list">
        <?= $this->render('task_list/header', array(
            'paginator' => $paginator,
        )) ?>

        <?php foreach ($paginator->getCollection() as $task): ?>
            <div class="table-list-row color-<?= $task['color_id'] ?>">
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
