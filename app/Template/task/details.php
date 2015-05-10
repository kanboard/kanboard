<div class="color-<?= $task['color_id'] ?> task-show-details">
    <h2><?= $this->e('#'.$task['id'].' '.$task['title']) ?></h2>
    <?php if ($task['score']): ?>
        <span class="task-score"><?= $this->e($task['score']) ?></span>
    <?php endif ?>
    <ul>
        <?php if ($task['reference']): ?>
        <li>
            <strong><?= t('Reference: %s', $task['reference']) ?></strong>
        </li>
        <?php endif ?>
        <li>
            <?= dt('Created on %B %e, %Y at %k:%M %p', $task['date_creation']) ?>
        </li>
        <?php if ($task['date_modification']): ?>
        <li>
            <?= dt('Last modified on %B %e, %Y at %k:%M %p', $task['date_modification']) ?>
        </li>
        <?php endif ?>
        <?php if ($task['date_completed']): ?>
        <li>
            <?= dt('Completed on %B %e, %Y at %k:%M %p', $task['date_completed']) ?>
        </li>
        <?php endif ?>
        <?php if ($task['date_started']): ?>
        <li>
            <?= dt('Started on %B %e, %Y', $task['date_started']) ?>
        </li>
        <?php endif ?>
        <?php if ($task['date_due']): ?>
        <li>
            <strong><?= dt('Must be done before %B %e, %Y', $task['date_due']) ?></strong>
        </li>
        <?php endif ?>
        <?php if ($task['time_estimated']): ?>
        <li>
            <?= t('Estimated time: %s hours', $task['time_estimated']) ?>
        </li>
        <?php endif ?>
        <?php if ($task['time_spent']): ?>
        <li>
            <?= t('Time spent: %s hours', $task['time_spent']) ?>
        </li>
        <?php endif ?>
        <?php if ($task['creator_username']): ?>
        <li>
            <?= t('Created by %s', $task['creator_name'] ?: $task['creator_username']) ?>
        </li>
        <?php endif ?>
        <li>
            <strong>
            <?php if ($task['assignee_username']): ?>
                <?= t('Assigned to %s', $task['assignee_name'] ?: $task['assignee_username']) ?>
            <?php else: ?>
                <?= t('There is nobody assigned') ?>
            <?php endif ?>
            </strong>
        </li>
        <li>
            <?= t('Column on the board:') ?>
            <strong><?= $this->e($task['column_title']) ?></strong>
            (<?= $this->e($task['project_name']) ?>)
            <?= dt('since %B %e, %Y at %k:%M %p', $task['date_moved']) ?>
        </li>
        <li><?= t('Task position:').' '.$this->e($task['position']) ?></li>
        <?php if ($task['category_name']): ?>
        <li>
            <?= t('Category:') ?> <strong><?= $this->e($task['category_name']) ?></strong>
        </li>
        <?php endif ?>
        <li>
            <?php if ($task['is_active'] == 1): ?>
                <?= t('Status is open') ?>
            <?php else: ?>
                <?= t('Status is closed') ?>
            <?php endif ?>
        </li>
        <?php if ($project['is_public']): ?>
        <li>
            <?= $this->a(t('Public link'), 'task', 'readonly', array('task_id' => $task['id'], 'token' => $project['token']), false, '', '', true) ?>
        </li>
        <?php endif ?>
        <?php if ($task['recurrence_status'] == \Model\Task::RECURE_STATUS_NONE): ?>
        <li>
            <?= t('No recurrent task is scheduled to generate') ?>
        </li>
        <?php endif ?>
        <?php if ($task['recurrence_status'] != \Model\Task::RECURE_STATUS_NONE): ?>
        <li>
            <?php if ($task['recurrence_status'] == \Model\Task::RECURE_STATUS_PENDING): ?>
            <?= t('Recurrent task is scheduled to generate') ?>
            <?php endif ?>
            <?php if ($task['recurrence_status'] == \Model\Task::RECURE_STATUS_PROCESSED): ?>
            <?= t('Recurrent task has been generated') ?>
            <?php endif ?>
            <ul>
                <li>
                    <?= t('Trigger to generate recurrent task: %s', $recurrence_trigger_list[$task['recurrence_trigger']]) ?></stong>
                </li>
                <li>
                    <?= t('Factor to calculate new due date: %s', $task['recurrence_factor']) ?></stong>
                </li>
                <li>
                    <?= t('Timeframe to calculate new due date: %s', $recurrence_timeframe_list[$task['recurrence_timeframe']]) ?></stong>
                </li>
                <li>
                    <?= t('Base date to calculate new due date: %s', $recurrence_basedate_list[$task['recurrence_basedate']]) ?></stong>
                </li>
            </ul>
        </li>
        <?php endif ?>
        <?php if ($task['recurrence_parent']): ?>
        <li>
            <?= t('Recurrent task created by: %s', $task['recurrence_parent']) ?>
        </li>
        <?php endif ?>
        <?php if ($task['recurrence_child']): ?>
        <li>
            <?= t('Created recurrent task: %s', $task['recurrence_child']) ?>
        </li>
        <?php endif ?>
    </ul>
</div>
