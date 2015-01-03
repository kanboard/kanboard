<?php if (! empty($subtasks)): ?>
<div id="subtasks" class="task-show-section">

    <div class="page-header">
        <h2><?= t('Sub-Tasks') ?></h2>
    </div>

    <table class="subtasks-table">
        <tr>
            <th class="column-40"><?= t('Title') ?></th>
            <th class="column-15"><?= t('Status') ?></th>
            <th><?= t('Assignee') ?></th>
            <th><?= t('Time tracking') ?></th>
            <?php if (! isset($not_editable)): ?>
                <th><?= t('Actions') ?></th>
            <?php endif ?>
        </tr>
        <?php foreach ($subtasks as $subtask): ?>
        <tr>
            <td><?= $this->e($subtask['title']) ?></td>
            <td>
                <?php if (! isset($not_editable)): ?>
                    <?= $this->a(trim($this->render('subtask/icons', array('subtask' => $subtask))) . $this->e($subtask['status_name']),
                                 'subtask', 'toggleStatus', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id'])) ?>
                <?php else: ?>
                    <?= $this->render('subtask/icons', array('subtask' => $subtask)) . $this->e($subtask['status_name']) ?>
                <?php endif ?>
            </td>
            <td>
                <?php if (! empty($subtask['username'])): ?>
                    <?= $this->e($subtask['name'] ?: $subtask['username']) ?>
                <?php endif ?>
            </td>
            <td>
                <?php if (! empty($subtask['time_spent'])): ?>
                    <strong><?= $this->e($subtask['time_spent']).'h' ?></strong> <?= t('spent') ?>
                <?php endif ?>

                <?php if (! empty($subtask['time_estimated'])): ?>
                    <strong><?= $this->e($subtask['time_estimated']).'h' ?></strong> <?= t('estimated') ?>
                <?php endif ?>
            </td>
            <?php if (! isset($not_editable)): ?>
                <td>
                    <ul>
                        <li>
                            <?= $this->a(t('Edit'), 'subtask', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id'])) ?>
                        </li>
                        <li>
                            <?= $this->a(t('Remove'), 'subtask', 'confirm', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id'])) ?>
                        </li>
                    </ul>
                </td>
            <?php endif ?>
        </tr>
        <?php endforeach ?>
    </table>

    <?php if (! isset($not_editable)): ?>
        <form method="post" action="<?= $this->u('subtask', 'save', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">
            <?= $this->formCsrf() ?>
            <?= $this->formHidden('task_id', array('task_id' => $task['id'])) ?>
            <?= $this->formText('title', array(), array(), array('required', 'placeholder="'.t('Type here to create a new sub-task').'"')) ?>
            <input type="submit" value="<?= t('Add') ?>" class="btn btn-blue"/>
        </form>
    <?php endif ?>

</div>
<?php endif ?>
