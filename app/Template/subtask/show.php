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
                    <?= $this->subtask->toggleStatus($subtask, 'task') ?>
                <?php else: ?>
                    <?= $this->render('subtask/icons', array('subtask' => $subtask)) . $this->e($subtask['title']) ?>
                <?php endif ?>
            </td>
            <td>
                <?php if (! empty($subtask['username'])): ?>
                    <?php if (! isset($not_editable)): ?>
                        <?= $this->url->link($this->e($subtask['name'] ?: $subtask['username']), 'user', 'show', array('user_id' => $subtask['user_id'])) ?>
                    <?php else: ?>
                        <?= $this->e($subtask['name'] ?: $subtask['username']) ?>
                    <?php endif ?>
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
                    <?php if (! isset($not_editable) && $subtask['user_id'] == $this->user->getId()): ?>
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
            <?php if (! isset($not_editable)): ?>
                <td>
                    <ul>
                        <?php if ($subtask['position'] != $first_position): ?>
                            <li>
                                <?= $this->url->link(t('Move Up'), 'subtask', 'movePosition', array('project_id' => $project['id'], 'task_id' => $subtask['task_id'], 'subtask_id' => $subtask['id'], 'direction' => 'up'), true) ?>
                            </li>
                        <?php endif ?>
                        <?php if ($subtask['position'] != $last_position): ?>
                            <li>
                                <?= $this->url->link(t('Move Down'), 'subtask', 'movePosition', array('project_id' => $project['id'], 'task_id' => $subtask['task_id'], 'subtask_id' => $subtask['id'], 'direction' => 'down'), true) ?>
                            </li>
                        <?php endif ?>
                        <li>
                            <?= $this->url->link(t('Edit'), 'subtask', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id'])) ?>
                        </li>
                        <li>
                            <?= $this->url->link(t('Remove'), 'subtask', 'confirm', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id'])) ?>
                        </li>
                    </ul>
                </td>
            <?php endif ?>
        </tr>
        <?php endforeach ?>
    </table>

    <?php if (! isset($not_editable)): ?>
        <form method="post" action="<?= $this->url->href('subtask', 'save', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">
            <?= $this->form->csrf() ?>
            <?= $this->form->hidden('task_id', array('task_id' => $task['id'])) ?>
            <?= $this->form->text('title', array(), array(), array('required', 'placeholder="'.t('Type here to create a new sub-task').'"')) ?>
            <?= $this->form->numeric('time_estimated', array(), array(), array('placeholder="'.t('Original estimate').'"')) ?>
            <?= $this->form->select('user_id', $users_list, array(), array(), array('placeholder="'.t('Assignee').'"')) ?>
            <input type="submit" value="<?= t('Add') ?>" class="btn btn-blue"/>
        </form>
    <?php endif ?>

</div>
<?php endif ?>
