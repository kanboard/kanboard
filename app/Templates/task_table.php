<table>
    <tr>
        <th><?= t('Id') ?></th>
        <th><?= t('Column') ?></th>
        <th><?= t('Category') ?></th>
        <th><?= t('Title') ?></th>
        <th><?= t('Assignee') ?></th>
        <th><?= t('Due date') ?></th>
        <th><?= t('Date created') ?></th>
        <th><?= t('Date completed') ?></th>
        <th><?= t('Status') ?></th>
    </tr>
    <?php foreach ($tasks as $task): ?>
    <tr>
        <td class="task-table task-<?= $task['color_id'] ?>">
            <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>" title="<?= t('View this task') ?>"><?= Helper\escape($task['id']) ?></a>
        </td>
        <td>
            <?= Helper\in_list($task['column_id'], $columns) ?>
        </td>
        <td>
            <?= Helper\in_list($task['category_id'], $categories, '') ?>
        </td>
        <td>
            <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>" title="<?= t('View this task') ?>"><?= Helper\escape($task['title']) ?></a>
        </td>
        <td>
            <?php if ($task['username']): ?>
                <?= Helper\escape($task['username']) ?>
            <?php else: ?>
                <?= t('Unassigned') ?>
            <?php endif ?>
        </td>
        <td>
            <?= dt('%B %e, %Y', $task['date_due']) ?>
        </td>
        <td>
            <?= dt('%B %e, %Y at %k:%M %p', $task['date_creation']) ?>
        </td>
        <td>
            <?php if ($task['date_completed']): ?>
                <?= dt('%B %e, %Y at %k:%M %p', $task['date_completed']) ?>
            <?php endif ?>
        </td>
        <td>
            <?php if ($task['is_active'] == \Model\Task::STATUS_OPEN): ?>
                <?= t('Open') ?>
            <?php else: ?>
                <?= t('Closed') ?>
            <?php endif ?>
        </td>
    </tr>
    <?php endforeach ?>
</table>