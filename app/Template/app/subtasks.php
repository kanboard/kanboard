<div class="page-header">
    <h2><?= t('My subtasks') ?> (<?= $paginator->getTotal() ?>)</h2>
</div>
<?php if ($paginator->isEmpty()): ?>
    <p class="alert"><?= t('There is nothing assigned to you.') ?></p>
<?php else: ?>
    <table class="table-fixed table-small">
        <tr>
            <th class="column-10"><?= $paginator->order('Id', 'tasks.id') ?></th>
            <th class="column-20"><?= $paginator->order(t('Project'), 'project_name') ?></th>
            <th><?= $paginator->order(t('Task'), 'task_name') ?></th>
            <th><?= $paginator->order(t('Subtask'), 'title') ?></th>
            <th class="column-20"><?= t('Time tracking') ?></th>
        </tr>
        <?php foreach ($paginator->getCollection() as $subtask): ?>
        <tr>
            <td class="task-table color-<?= $subtask['color_id'] ?>">
                <?= $this->url->link('#'.$subtask['task_id'], 'task', 'show', array('task_id' => $subtask['task_id'], 'project_id' => $subtask['project_id'])) ?>
            </td>
            <td>
                <?= $this->url->link($this->e($subtask['project_name']), 'board', 'show', array('project_id' => $subtask['project_id'])) ?>
            </td>
            <td>
                <?= $this->url->link($this->e($subtask['task_name']), 'task', 'show', array('task_id' => $subtask['task_id'], 'project_id' => $subtask['project_id'])) ?>
            </td>
            <td>
                <?= $this->subtask->toggleStatus($subtask, 'dashboard') ?>
            </td>
            <td>
                <?php if (! empty($subtask['time_spent'])): ?>
                    <strong><?= $this->e($subtask['time_spent']).'h' ?></strong> <?= t('spent') ?>
                <?php endif ?>

                <?php if (! empty($subtask['time_estimated'])): ?>
                    <strong><?= $this->e($subtask['time_estimated']).'h' ?></strong> <?= t('estimated') ?>
                <?php endif ?>
            </td>
        </tr>
        <?php endforeach ?>
    </table>

    <?= $paginator ?>
<?php endif ?>