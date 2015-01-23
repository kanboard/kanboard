<h2><?= t('My tasks') ?></h2>
<?php if ($paginator->isEmpty()): ?>
    <p class="alert"><?= t('There is nothing assigned to you.') ?></p>
<?php else: ?>
    <table class="table-fixed">
        <tr>
            <th class="column-8"><?= $paginator->order('Id', 'tasks.id') ?></th>
            <th class="column-20"><?= $paginator->order(t('Project'), 'project_name') ?></th>
            <th><?= $paginator->order(t('Task'), 'title') ?></th>
            <th class="column-20"><?= $paginator->order(t('Due date'), 'date_due') ?></th>
        </tr>
        <?php foreach ($paginator->getCollection() as $task): ?>
        <tr>
            <td class="task-table color-<?= $task['color_id'] ?>">
                <?= $this->a('#'.$task['id'], 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
            </td>
            <td>
                <?= $this->a($this->e($task['project_name']), 'board', 'show', array('project_id' => $task['project_id'])) ?>
            </td>
            <td>
                <?= $this->a($this->e($task['title']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
            </td>
            <td>
                <?= dt('%B %e, %Y', $task['date_due']) ?>
            </td>
        </tr>
        <?php endforeach ?>
    </table>

    <?= $paginator ?>
<?php endif ?>