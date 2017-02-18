<?php if (! empty($subtasks)): ?>
    <table
        class="subtasks-table table-striped table-scrolling"
        data-save-position-url="<?= $this->url->href('SubtaskController', 'movePosition', array('project_id' => $task['project_id'], 'task_id' => $task['id'])) ?>"
    >
    <thead>
        <tr>
            <th class="column-45"><?= t('Title') ?></th>
            <th class="column-15"><?= t('Assignee') ?></th>
            <?= $this->hook->render('template:subtask:table:header:before-timetracking') ?>
            <th><?= t('Time tracking') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($subtasks as $subtask): ?>
        <tr data-subtask-id="<?= $subtask['id'] ?>">
            <td>
                <?php if ($editable): ?>
                    <i class="fa fa-arrows-alt draggable-row-handle" title="<?= t('Change subtask position') ?>"></i>&nbsp;
                    <?= $this->render('subtask/menu', array(
                        'task' => $task,
                        'subtask' => $subtask,
                    )) ?>
                    <?= $this->subtask->toggleStatus($subtask, $task['project_id'], true) ?>
                <?php else: ?>
                    <?= $this->subtask->getTitle($subtask) ?>
                <?php endif ?>
            </td>
            <td>
                <?php if (! empty($subtask['username'])): ?>
                    <?= $this->text->e($subtask['name'] ?: $subtask['username']) ?>
                <?php endif ?>
            </td>
            <?= $this->hook->render('template:subtask:table:rows', array('subtask' => $subtask)) ?>
            <td>
                <?php if (! empty($subtask['time_spent'])): ?>
                    <strong><?= $this->text->e($subtask['time_spent']).'h' ?></strong> <?= t('spent') ?>
                <?php endif ?>

                <?php if (! empty($subtask['time_estimated'])): ?>
                    <strong><?= $this->text->e($subtask['time_estimated']).'h' ?></strong> <?= t('estimated') ?>
                <?php endif ?>

                <?php if ($editable && $subtask['user_id'] == $this->user->getId()): ?>
                    <?php if ($subtask['is_timer_started']): ?>
                        <?= $this->url->icon('pause', t('Stop timer'), 'SubtaskStatusController', 'timer', array('timer' => 'stop', 'project_id' => $task['project_id'], 'task_id' => $subtask['task_id'], 'subtask_id' => $subtask['id']), false, 'subtask-toggle-timer') ?>
                        (<?= $this->dt->age($subtask['timer_start_date']) ?>)
                    <?php else: ?>
                        <?= $this->url->icon('play-circle-o', t('Start timer'), 'SubtaskStatusController', 'timer', array('timer' => 'start', 'project_id' => $task['project_id'], 'task_id' => $subtask['task_id'], 'subtask_id' => $subtask['id']), false, 'subtask-toggle-timer') ?>
                    <?php endif ?>
                <?php endif ?>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
    </table>
<?php endif ?>
