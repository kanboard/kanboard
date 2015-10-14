<table class="table-fixed table-small">
    <tr>
        <th class="column-8"><?= $paginator->order(t('Project'), 'tasks.project_id') ?></th>
        <th class="column-5"><?= $paginator->order(t('Id'), 'tasks.id') ?></th>
        <th class="column-10"><?= $paginator->order(t('Swimlane'), 'tasks.swimlane_id') ?></th>
        <th class="column-10"><?= $paginator->order(t('Column'), 'tasks.column_id') ?></th>
        <th class="column-10"><?= $paginator->order(t('Category'), 'tasks.category_id') ?></th>
        <th><?= $paginator->order(t('Title'), 'tasks.title') ?></th>
        <th class="column-10"><?= $paginator->order(t('Assignee'), 'users.username') ?></th>
        <th class="column-10"><?= $paginator->order(t('Due date'), 'tasks.date_due') ?></th>
        <th class="column-5"><?= $paginator->order(t('Status'), 'tasks.is_active') ?></th>
    </tr>
    <?php foreach ($paginator->getCollection() as $task): ?>
    <tr>
        <td>
            <?= $this->url->link($this->e($task['project_name']), 'board', 'show', array('project_id' => $task['project_id'])) ?>
        </td>
        <td class="task-table color-<?= $task['color_id'] ?>">
            <?= $this->url->link('#'.$this->e($task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, '', t('View this task')) ?>
        </td>
        <td>
            <?= $this->e($task['swimlane_name'] ?: $task['default_swimlane']) ?>
        </td>
        <td>
            <?= $this->e($task['column_name']) ?>
        </td>
        <td>
            <?= $this->e($task['category_name']) ?>
        </td>
        <td>
            <?= $this->url->link($this->e($task['title']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, '', t('View this task')) ?>
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
            <?php if ($task['is_active'] == \Kanboard\Model\Task::STATUS_OPEN): ?>
                <?= t('Open') ?>
            <?php else: ?>
                <?= t('Closed') ?>
            <?php endif ?>
        </td>
    </tr>
    <?php endforeach ?>
</table>

<?= $paginator ?>
