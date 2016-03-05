<div class="page-header">
    <h2><?= $this->url->link(t('My tasks'), 'app', 'tasks', array('user_id' => $user['id'])) ?> (<?= $paginator->getTotal() ?>)</h2>
</div>
<?php if ($paginator->isEmpty()): ?>
    <p class="alert"><?= t('There is nothing assigned to you.') ?></p>
<?php else: ?>
    <table class="table-fixed table-small">
        <tr>
            <th class="column-5"><?= $paginator->order('Id', 'tasks.id') ?></th>
            <th class="column-20"><?= $paginator->order(t('Project'), 'project_name') ?></th>
            <th><?= $paginator->order(t('Task'), 'title') ?></th>
            <th class="column-20"><?= t('Time tracking') ?></th>
            <th class="column-20"><?= $paginator->order(t('Due date'), 'date_due') ?></th>
        </tr>
        <?php foreach ($paginator->getCollection() as $task): ?>
        <tr>
            <td class="task-table color-<?= $task['color_id'] ?>">
                <?= $this->render('task/dropdown', array('task' => $task)) ?>
            </td>
            <td>
                <?= $this->url->link($this->text->e($task['project_name']), 'board', 'show', array('project_id' => $task['project_id'])) ?>
            </td>
            <td>
                <?= $this->url->link($this->text->e($task['title']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
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
        </tr>
        <?php endforeach ?>
    </table>

    <?= $paginator ?>
<?php endif ?>