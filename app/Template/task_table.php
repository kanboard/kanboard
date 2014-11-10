<table class="table-fixed table-small">
    <tr>
        <th class="column-8"><?= Helper\order(t('Id'), 'tasks.id', $pagination) ?></th>
        <th class="column-8"><?= Helper\order(t('Column'), 'tasks.column_id', $pagination) ?></th>
        <th class="column-8"><?= Helper\order(t('Category'), 'tasks.category_id', $pagination) ?></th>
        <th><?= Helper\order(t('Title'), 'tasks.title', $pagination) ?></th>
        <th class="column-10"><?= Helper\order(t('Assignee'), 'users.username', $pagination) ?></th>
        <th class="column-10"><?= Helper\order(t('Due date'), 'tasks.date_due', $pagination) ?></th>
        <th class="column-10"><?= Helper\order(t('Date created'), 'tasks.date_creation', $pagination) ?></th>
        <th class="column-10"><?= Helper\order(t('Date completed'), 'tasks.date_completed', $pagination) ?></th>
        <th class="column-5"><?= Helper\order(t('Status'), 'tasks.is_active', $pagination) ?></th>
    </tr>
    <?php foreach ($tasks as $task): ?>
    <tr>
        <td class="task-table task-<?= $task['color_id'] ?>">
            <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>" title="<?= t('View this task') ?>">#<?= Helper\escape($task['id']) ?></a>
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
            <?php if ($task['assignee_username']): ?>
                <?= Helper\escape($task['assignee_name'] ?: $task['assignee_username']) ?>
            <?php else: ?>
                <?= t('Unassigned') ?>
            <?php endif ?>
        </td>
        <td>
            <?= dt('%B %e, %Y', $task['date_due']) ?>
        </td>
        <td>
            <?= dt('%B %e, %Y', $task['date_creation']) ?>
        </td>
        <td>
            <?php if ($task['date_completed']): ?>
                <?= dt('%B %e, %Y', $task['date_completed']) ?>
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

<?= Helper\paginate($pagination) ?>
