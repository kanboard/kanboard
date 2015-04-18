<?php if (! empty($subtasks)): ?>

<?php $first_position = $subtasks[0]['position']; ?>
<?php $last_position = $subtasks[count($subtasks) - 1]['position']; ?>

<div id="subtasks" class="task-show-section">

    <div class="page-header">
        <h2><?= t('Sub-Tasks') ?></h2>
    </div>

    <table class="subtasks-table">
        <tr>
            <th class="column-40"><?= t('Title') ?></th>
            <th><?= t('Assignee') ?></th>
            <th><?= t('Time tracking') ?></th>
            <?php if (! isset($not_editable)): ?>
                <th><?= t('Actions') ?></th>
            <?php endif ?>
        </tr>
        <?php foreach ($subtasks as $subtask): ?>
        <tr>
            <td>
                <?php if (! isset($not_editable)): ?>
                    <?= $this->toggleSubtaskStatus($subtask, 'task') ?>
                <?php else: ?>
                    <?= $this->render('subtask/icons', array('subtask' => $subtask)) . $this->e($subtask['title']) ?>
                <?php endif ?>
            </td>
            <td>
                <?php if (! empty($subtask['username'])): ?>
                    <?= $this->a($this->e($subtask['name'] ?: $subtask['username']), 'user', 'show', array('user_id' => $subtask['user_id'])) ?>
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
                        <?php if ($subtask['position'] != $first_position): ?>
                            <li>
                                <?= $this->a(t('Move Up'), 'subtask', 'movePosition', array('project_id' => $project['id'], 'task_id' => $subtask['task_id'], 'subtask_id' => $subtask['id'], 'direction' => 'up'), true) ?>
                            </li>
                        <?php endif ?>
                        <?php if ($subtask['position'] != $last_position): ?>
                            <li>
                                <?= $this->a(t('Move Down'), 'subtask', 'movePosition', array('project_id' => $project['id'], 'task_id' => $subtask['task_id'], 'subtask_id' => $subtask['id'], 'direction' => 'down'), true) ?>
                            </li>
                        <?php endif ?>
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
