<?php if (! empty($subtasks)): ?>

    <?php $first_position = $subtasks[0]['position']; ?>
    <?php $last_position = $subtasks[count($subtasks) - 1]['position']; ?>

    <table class="subtasks-table">
        <tr>
            <th class="column-40"><?= t('Title') ?></th>
            <th><?= t('Assignee') ?></th>
            <th><?= t('Time tracking') ?></th>
            <?php if ($editable): ?>
                <th class="column-5"></th>
            <?php endif ?>
        </tr>
        <?php foreach ($subtasks as $subtask): ?>
        <tr>
            <td>
                <?php if ($editable): ?>
                    <?= $this->subtask->toggleStatus($subtask, $task['project_id']) ?>
                <?php else: ?>
                    <?= $this->subtask->getTitle($subtask) ?>
                <?php endif ?>
            </td>
            <td>
                <?php if (! empty($subtask['username'])): ?>
                    <?= $this->e($subtask['name'] ?: $subtask['username']) ?>
                <?php endif ?>
            </td>
            <td>
                <ul class="no-bullet">
                    <li>
                        <?php if (! empty($subtask['time_spent'])): ?>
                            <strong><?= $this->e($subtask['time_spent']).'h' ?></strong> <?= t('spent') ?>
                        <?php endif ?>

                        <?php if (! empty($subtask['time_estimated'])): ?>
                            <strong><?= $this->e($subtask['time_estimated']).'h' ?></strong> <?= t('estimated') ?>
                        <?php endif ?>
                    </li>
                    <?php if ($editable && $subtask['user_id'] == $this->user->getId()): ?>
                    <li>
                        <?php if ($subtask['is_timer_started']): ?>
                            <i class="fa fa-pause"></i>
                            <?= $this->url->link(t('Stop timer'), 'timer', 'subtask', array('timer' => 'stop', 'project_id' => $task['project_id'], 'task_id' => $subtask['task_id'], 'subtask_id' => $subtask['id'])) ?>
                            (<?= $this->dt->age($subtask['timer_start_date']) ?>)
                        <?php else: ?>
                            <i class="fa fa-play-circle-o"></i>
                            <?= $this->url->link(t('Start timer'), 'timer', 'subtask', array('timer' => 'start', 'project_id' => $task['project_id'], 'task_id' => $subtask['task_id'], 'subtask_id' => $subtask['id'])) ?>
                        <?php endif ?>
                    </li>
                    <?php endif ?>
                </ul>
            </td>
            <?php if ($editable): ?>
                <td>
                    <?= $this->render('subtask/menu', array(
                        'task' => $task,
                        'subtask' => $subtask,
                        'first_position' => $first_position,
                        'last_position' => $last_position,
                    )) ?>
                </td>
            <?php endif ?>
        </tr>
        <?php endforeach ?>
    </table>
<?php else: ?>
    <p class="alert"><?= t('There is no subtask at the moment.') ?></p>
<?php endif ?>
