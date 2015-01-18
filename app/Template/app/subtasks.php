<h2><?= t('My subtasks') ?></h2>
<?php if ($paginator->isEmpty()): ?>
    <p class="alert"><?= t('There is nothing assigned to you.') ?></p>
<?php else: ?>
    <table class="table-fixed">
        <tr>
            <th class="column-10"><?= $paginator->order('Id', 'tasks.id') ?></th>
            <th class="column-20"><?= $paginator->order(t('Project'), 'project_name') ?></th>
            <th class="column-15"><?= $paginator->order(t('Status'), 'status') ?></th>
            <th><?= $paginator->order(t('Subtask'), 'title') ?></th>
        </tr>
        <?php foreach ($paginator->getCollection() as $subtask): ?>
        <tr>
            <td class="task-table color-<?= $subtask['color_id'] ?>">
                <?= $this->a('#'.$subtask['task_id'], 'task', 'show', array('task_id' => $subtask['task_id'], 'project_id' => $subtask['project_id'])) ?>
            </td>
            <td>
                <?= $this->a($this->e($subtask['project_name']), 'board', 'show', array('project_id' => $subtask['project_id'])) ?>
            </td>
            <td>
                <?= $this->e($subtask['status_name']) ?>
            </td>
            <td>
                <?= $this->a($this->e($subtask['title']), 'task', 'show', array('task_id' => $subtask['task_id'], 'project_id' => $subtask['project_id'])) ?>
            </td>
        </tr>
        <?php endforeach ?>
    </table>

    <?= $paginator ?>
<?php endif ?>