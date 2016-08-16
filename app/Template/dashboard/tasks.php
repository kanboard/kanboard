<div class="page-header">
    <h2><?= $this->url->link(t('My tasks'), 'DashboardController', 'tasks', array('user_id' => $user['id'])) ?> (<?= $paginator->getTotal() ?>)</h2>
</div>
<?php if ($paginator->isEmpty()): ?>
    <p class="alert"><?= t('There is nothing assigned to you.') ?></p>
<?php else: ?>
    <table class="table-striped table-small table-scrolling">
        <tr>
            <th class="column-5"><?= $paginator->order('Id', \Kanboard\Model\TaskModel::TABLE.'.id') ?></th>
            <th class="column-20"><?= $paginator->order(t('Project'), 'project_name') ?></th>
            <th><?= $paginator->order(t('Task'), \Kanboard\Model\TaskModel::TABLE.'.title') ?></th>
            <th class="column-8"><?= $paginator->order(t('Priority'), \Kanboard\Model\TaskModel::TABLE.'.priority') ?></th>
            <th class="column-20"><?= t('Time tracking') ?></th>
            <th class="column-10"><?= $paginator->order(t('Due date'), \Kanboard\Model\TaskModel::TABLE.'.date_due') ?></th>
            <th class="column-10"><?= $paginator->order(t('Column'), 'column_title') ?></th>
        </tr>
        <?php foreach ($paginator->getCollection() as $task): ?>
        <tr>
            <td class="task-table color-<?= $task['color_id'] ?>">
                <?= $this->render('task/dropdown', array('task' => $task)) ?>
            </td>
            <td>
                <?= $this->url->link($this->text->e($task['project_name']), 'BoardViewController', 'show', array('project_id' => $task['project_id'])) ?>
            </td>
            <td>
                <?= $this->url->link($this->text->e($task['title']), 'TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
            </td>
            <td>
                <?php if ($task['priority'] >= 0): ?>
                    P<?= $this->text->e($task['priority'])?>
                <?php endif?>
            </td>
            <td>
                <?php if (! empty($task['time_spent'])): ?>
                    <strong><?= $this->text->e($task['time_spent']).'h' ?></strong> <?= t('spent') ?>
                <?php endif ?>

                <?php if (! empty($task['time_estimated'])): ?>
                    <strong><?= $this->text->e($task['time_estimated']).'h' ?></strong> <?= t('estimated') ?>
                <?php endif ?>
            </td>
            <td>
                <?= $this->dt->date($task['date_due']) ?>
            </td>
            <td>
                <?= $this->text->e($task['column_title']) ?>
            </td>
        </tr>
        <?php endforeach ?>
    </table>

    <?= $paginator ?>
<?php endif ?>
