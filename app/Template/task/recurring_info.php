<ul>
    <?php if ($task['recurrence_status'] == \Model\Task::RECURRING_STATUS_PENDING): ?>
        <li><?= t('Recurrent task is scheduled to be generated') ?></li>
    <?php elseif ($task['recurrence_status'] == \Model\Task::RECURRING_STATUS_PROCESSED): ?>
        <li><?= t('Recurrent task has been generated:') ?>
            <ul>
                <li>
                    <?= t('Trigger to generate recurrent task: ') ?><strong><?= $this->e($recurrence_trigger_list[$task['recurrence_trigger']]) ?></strong>
                </li>
                <li>
                    <?= t('Factor to calculate new due date: ') ?><strong><?= $this->e($task['recurrence_factor']) ?></strong>
                </li>
                <li>
                    <?= t('Timeframe to calculate new due date: ') ?><strong><?= $this->e($recurrence_timeframe_list[$task['recurrence_timeframe']]) ?></strong>
                </li>
                <li>
                    <?= t('Base date to calculate new due date: ') ?><strong><?= $this->e($recurrence_basedate_list[$task['recurrence_basedate']]) ?></strong>
                </li>
            </ul>
        </li>
    <?php endif ?>

    <?php if ($task['recurrence_parent'] || $task['recurrence_child']): ?>
        <?php if ($task['recurrence_parent']): ?>
        <li>
            <?= t('This task has been created by: ') ?>
            <?= $this->a('#'.$task['recurrence_parent'], 'task', 'show', array('task_id' => $task['recurrence_parent'], 'project_id' => $task['project_id'])) ?>
        </li>
        <?php endif ?>
        <?php if ($task['recurrence_child']): ?>
        <li>
            <?= t('This task has created this child task: ') ?>
            <?= $this->a('#'.$task['recurrence_child'], 'task', 'show', array('task_id' => $task['recurrence_child'], 'project_id' => $task['project_id'])) ?>
        </li>
        <?php endif ?>
    <?php endif ?>
</ul>