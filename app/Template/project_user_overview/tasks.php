<?php if ($paginator->isEmpty()): ?>
    <p class="alert"><?= t('No tasks found.') ?></p>
<?php elseif (! $paginator->isEmpty()): ?>
    <table class="table-small table-striped table-scrolling">
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
                <?= $this->url->link('#'.$this->text->e($task['id']), 'TaskViewController', 'show', array('task_id' => $task['id']), false, '', t('View this task')) ?>
            </td>
            <td>
                <?= $this->url->link($this->text->e($task['project_name']), 'BoardViewController', 'show', array('project_id' => $task['project_id'])) ?>
            </td>
            <td>
                <?= $this->text->e($task['column_name']) ?>
            </td>
            <td>
                <?= $this->url->link($this->text->e($task['title']), 'TaskViewController', 'show', array('task_id' => $task['id']), false, '', t('View this task')) ?>
            </td>
            <td>
                <?php if ($task['assignee_username']): ?>
                    <?= $this->text->e($task['assignee_name'] ?: $task['assignee_username']) ?>
                <?php else: ?>
                    <?= t('Unassigned') ?>
                <?php endif ?>
            </td>
            <td>
                <?= $this->dt->date($task['date_started']) ?>
            </td>
            <td>
                <?= $this->dt->datetime($task['date_due']) ?>
            </td>
        </tr>
        <?php endforeach ?>
    </table>

    <?= $paginator ?>
<?php endif ?>
