<h2><?= t('My tasks') ?></h2>
<?php if (empty($tasks)): ?>
    <p class="alert"><?= t('There is nothing assigned to you.') ?></p>
<?php else: ?>
    <table class="table-fixed">
        <tr>
            <th class="column-8"><?= $this->order('Id', 'tasks.id', $pagination) ?></th>
            <th class="column-20"><?= $this->order(t('Project'), 'project_name', $pagination) ?></th>
            <th><?= $this->order(t('Task'), 'title', $pagination) ?></th>
            <th class="column-20"><?= $this->order(t('Due date'), 'date_due', $pagination) ?></th>
        </tr>
        <?php foreach ($tasks as $task): ?>
        <tr>
            <td class="task-table task-<?= $task['color_id'] ?>">
                <?= $this->a('#'.$task['id'], 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
            </td>
            <td>
                <?= $this->a($this->e($task['project_name']), 'board', 'show', array('project_id' => $task['project_id'])) ?>
            </td>
            <td>
                <?= $this->a($this->e($task['title']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
            </td>
            <td>
                <?= dt('%B %e, %Y', $task['date_due']) ?>
            </td>
        </tr>
        <?php endforeach ?>
    </table>

    <?= $this->paginate($pagination) ?>
<?php endif ?>