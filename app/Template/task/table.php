<table class="table-fixed table-small">
    <tr>
        <th class="column-8"><?= $this->order(t('Id'), 'tasks.id', $pagination) ?></th>
        <th class="column-8"><?= $this->order(t('Column'), 'tasks.column_id', $pagination) ?></th>
        <th class="column-8"><?= $this->order(t('Category'), 'tasks.category_id', $pagination) ?></th>
        <th><?= $this->order(t('Title'), 'tasks.title', $pagination) ?></th>
        <th class="column-10"><?= $this->order(t('Assignee'), 'users.username', $pagination) ?></th>
        <th class="column-10"><?= $this->order(t('Due date'), 'tasks.date_due', $pagination) ?></th>
        <th class="column-10"><?= $this->order(t('Date created'), 'tasks.date_creation', $pagination) ?></th>
        <th class="column-10"><?= $this->order(t('Date completed'), 'tasks.date_completed', $pagination) ?></th>
        <th class="column-5"><?= $this->order(t('Status'), 'tasks.is_active', $pagination) ?></th>
    </tr>
    <?php foreach ($tasks as $task): ?>
    <tr>
        <td class="task-table task-<?= $task['color_id'] ?>">
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

<?= $this->paginate($pagination) ?>
