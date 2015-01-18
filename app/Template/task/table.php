<table class="table-fixed table-small">
    <tr>
        <th class="column-8"><?= $paginator->order(t('Id'), 'tasks.id') ?></th>
        <th class="column-8"><?= $paginator->order(t('Column'), 'tasks.column_id') ?></th>
        <th class="column-8"><?= $paginator->order(t('Category'), 'tasks.category_id') ?></th>
        <th><?= $paginator->order(t('Title'), 'tasks.title') ?></th>
        <th class="column-10"><?= $paginator->order(t('Assignee'), 'users.username') ?></th>
        <th class="column-10"><?= $paginator->order(t('Due date'), 'tasks.date_due') ?></th>
        <th class="column-10"><?= $paginator->order(t('Date created'), 'tasks.date_creation') ?></th>
        <th class="column-10"><?= $paginator->order(t('Date completed'), 'tasks.date_completed') ?></th>
        <th class="column-5"><?= $paginator->order(t('Status'), 'tasks.is_active') ?></th>
    </tr>
    <?php foreach ($paginator->getCollection() as $task): ?>
    <tr>
        <td class="task-table color-<?= $task['color_id'] ?>">
            <?= $this->a('#'.$this->e($task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, '', t('View this task')) ?>
        </td>
        <td>
            <?= $this->inList($task['column_id'], $columns) ?>
        </td>
        <td>
            <?= $this->inList($task['category_id'], $categories, '') ?>
        </td>
        <td>
            <?= $this->a($this->e($task['title']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, '', t('View this task')) ?>
        </td>
        <td>
            <?php if ($task['assignee_username']): ?>
                <?= $this->e($task['assignee_name'] ?: $task['assignee_username']) ?>
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

<?= $paginator ?>
