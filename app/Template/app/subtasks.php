<h2><?= t('My subtasks') ?></h2>
<?php if (empty($subtasks)): ?>
    <p class="alert"><?= t('There is nothing assigned to you.') ?></p>
<?php else: ?>
    <table class="table-fixed">
        <tr>
            <th class="column-10"><?= Helper\order('Id', 'tasks.id', $pagination) ?></th>
            <th class="column-20"><?= Helper\order(t('Project'), 'project_name', $pagination) ?></th>
            <th class="column-15"><?= Helper\order(t('Status'), 'status', $pagination) ?></th>
            <th><?= Helper\order(t('Subtask'), 'title', $pagination) ?></th>
        </tr>
        <?php foreach ($subtasks as $subtask): ?>
        <tr>
            <td class="task-table task-<?= $subtask['color_id'] ?>">
                <?= Helper\a('#'.$subtask['task_id'], 'task', 'show', array('task_id' => $subtask['task_id'])) ?>
            </td>
            <td>
                <?= Helper\a(Helper\escape($subtask['project_name']), 'board', 'show', array('project_id' => $subtask['project_id'])) ?>
            </td>
            <td>
                <?= Helper\escape($subtask['status_name']) ?>
            </td>
            <td>
                <?= Helper\a(Helper\escape($subtask['title']), 'task', 'show', array('task_id' => $subtask['task_id'])) ?>
            </td>
        </tr>
        <?php endforeach ?>
    </table>

    <?= Helper\paginate($pagination) ?>
<?php endif ?>