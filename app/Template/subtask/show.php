<?php if (! empty($subtasks)): ?>
<div id="subtasks" class="task-show-section">

    <div class="page-header">
        <h2><?= t('Sub-Tasks') ?></h2>
    </div>

    <table class="subtasks-table">
        <tr>
            <th width="40%"><?= t('Title') ?></th>
            <th><?= t('Status') ?></th>
            <th><?= t('Assignee') ?></th>
            <th><?= t('Time tracking') ?></th>
            <?php if (! isset($not_editable)): ?>
                <th><?= t('Actions') ?></th>
            <?php endif ?>
        </tr>
        <?php foreach ($subtasks as $subtask): ?>
        <tr>
            <td><?= Helper\escape($subtask['title']) ?></td>
            <td>
                <?php if (! isset($not_editable)): ?>
                    <?= Helper\a(trim(Helper\template('subtask/icons', array('subtask' => $subtask))) . Helper\escape($subtask['status_name']),
                                 'subtask', 'toggleStatus', array('task_id' => $task['id'], 'subtask_id' => $subtask['id'])) ?>
                <?php else: ?>
                    <?= Helper\template('subtask/icons', array('subtask' => $subtask)) . Helper\escape($subtask['status_name']) ?>
                <?php endif ?>
            </td>
            <td>
                <?php if (! empty($subtask['username'])): ?>
                    <?= Helper\escape($subtask['name'] ?: $subtask['username']) ?>
                <?php endif ?>
            </td>
            <td>
                <?php if (! empty($subtask['time_spent'])): ?>
                    <strong><?= Helper\escape($subtask['time_spent']).'h' ?></strong> <?= t('spent') ?>
                <?php endif ?>

                <?php if (! empty($subtask['time_estimated'])): ?>
                    <strong><?= Helper\escape($subtask['time_estimated']).'h' ?></strong> <?= t('estimated') ?>
                <?php endif ?>
            </td>
            <?php if (! isset($not_editable)): ?>
                <td>
                    <?= Helper\a(t('Edit'), 'subtask', 'edit', array('task_id' => $task['id'], 'subtask_id' => $subtask['id'])) ?>
                    <?= t('or') ?>
                    <?= Helper\a(t('Remove'), 'subtask', 'confirm', array('task_id' => $task['id'], 'subtask_id' => $subtask['id'])) ?>
                </td>
            <?php endif ?>
        </tr>
        <?php endforeach ?>
    </table>

    <?php if (! isset($not_editable)): ?>
        <form method="post" action="<?= Helper\u('subtask', 'save', array('task_id' => $task['id'])) ?>" autocomplete="off">
            <?= Helper\form_csrf() ?>
            <?= Helper\form_hidden('task_id', array('task_id' => $task['id'])) ?>
            <?= Helper\form_text('title', array(), array(), array('required', 'placeholder="'.t('Type here to create a new sub-task').'"')) ?>
            <input type="submit" value="<?= t('Add') ?>" class="btn btn-blue"/>
        </form>
    <?php endif ?>

</div>
<?php endif ?>
