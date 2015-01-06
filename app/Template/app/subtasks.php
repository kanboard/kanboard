<h2><?= t('My subtasks') ?></h2>
<?php if (empty($subtasks)): ?>
    <p class="alert"><?= t('There is nothing assigned to you.') ?></p>
<?php else: ?>
    <table class="table-fixed">
        <tr>
            <th class="column-10"><?= $this->order('Id', 'tasks.id', $pagination) ?></th>
            <th class="column-20"><?= $this->order(t('Project'), 'project_name', $pagination) ?></th>
            <th class="column-15"><?= $this->order(t('Status'), 'status', $pagination) ?></th>
            <th><?= $this->order(t('Subtask'), 'title', $pagination) ?></th>
        </tr>
        <?php foreach ($subtasks as $subtask): ?>
        <tr>
            <td class="task-table task-<?= $subtask['color_id'] ?>">
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

    <?= $this->paginate($pagination) ?>
<?php endif ?>