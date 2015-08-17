<?php if ($paginator->isEmpty()): ?>
    <p class="alert"><?= t('No tasks found.') ?></p>
<?php elseif (! $paginator->isEmpty()): ?>
    <table class="table-small">
        <tr>
            <th class="column-5"><?= $paginator->order(t('Id'), 'tasks.id') ?></th>
            <th class="column-10"><?= $paginator->order(t('Project'), 'projects.name') ?></th>
            <th class="column-15"><?= $paginator->order(t('Column'), 'tasks.column_id') ?></th>
            <th><?= $paginator->order(t('Title'), 'tasks.title') ?></th>
            <th class="column-15"><?= $paginator->order(t('Assignee'), 'users.username') ?></th>
            <th class="column-15"><?= $paginator->order(t('Start date'), 'tasks.date_started') ?></th>
            <th class="column-15"><?= $paginator->order(t('Due date'), 'tasks.date_due') ?></th>
        </tr>
        <?php foreach ($paginator->getCollection() as $task): ?>
        <tr>
            <td class="task-table color-<?= $task['color_id'] ?>">
                <?= $this->url->link('#'.$this->e($task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, '', t('View this task')) ?>
            </td>
            <td>
                <?= $this->url->link($this->e($task['project_name']), 'board', 'show', array('project_id' => $task['project_id'])) ?>
            </td>
            <td>
                <?= $this->e($task['column_name']) ?>
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
                <?= dt('%B %e, %Y', $task['date_started']) ?>
            </td>
            <td>
                <?= dt('%B %e, %Y', $task['date_due']) ?>
            </td>
        </tr>
        <?php endforeach ?>
    </table>

    <?= $paginator ?>
<?php endif ?>
