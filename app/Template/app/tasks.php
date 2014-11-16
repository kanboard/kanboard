<h2><?= t('My tasks') ?></h2>
<?php if (empty($tasks)): ?>
    <p class="alert"><?= t('There is nothing assigned to you.') ?></p>
<?php else: ?>
    <table class="table-fixed">
        <tr>
            <th class="column-8"><?= Helper\order('Id', 'tasks.id', $pagination) ?></th>
            <th class="column-20"><?= Helper\order(t('Project'), 'project_name', $pagination) ?></th>
            <th><?= Helper\order(t('Task'), 'title', $pagination) ?></th>
            <th class="column-20"><?= Helper\order(t('Due date'), 'date_due', $pagination) ?></th>
        </tr>
        <?php foreach ($tasks as $task): ?>
        <tr>
            <td class="task-table task-<?= $task['color_id'] ?>">
                <?= Helper\a('#'.$task['id'], 'task', 'show', array('task_id' => $task['id'])) ?>
            </td>
            <td>
                <?= Helper\a(Helper\escape($task['project_name']), 'board', 'show', array('project_id' => $task['project_id'])) ?>
            </td>
            <td>
                <?= Helper\a(Helper\escape($task['title']), 'task', 'show', array('task_id' => $task['id'])) ?>
            </td>
            <td>
                <?= dt('%B %e, %Y', $task['date_due']) ?>
            </td>
        </tr>
        <?php endforeach ?>
    </table>

    <?= Helper\paginate($pagination) ?>
<?php endif ?>