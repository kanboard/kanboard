<section class="tooltip-large">
<?php if ($task['recurrence_status'] == \Model\Task::RECURE_STATUS_PENDING): ?>
    <?= t('Recurrent task is scheduled to generate') ?><br/>
<?php endif ?>
<?php if ($task['recurrence_status'] == \Model\Task::RECURE_STATUS_PROCESSED): ?>
    <?= t('Recurrent task has been generated') ?><br/>
<?php endif ?>
    <?= t('Trigger to generate recurrent task: %s', $recurrence_trigger_list[$task['recurrence_trigger']]) ?><br/>
    <?= t('Factor to calculate new due date: %s', $task['recurrence_factor']) ?><br/>
    <?= t('Timeframe to calculate new due date: %s', $recurrence_timeframe_list[$task['recurrence_timeframe']]) ?><br/>
    <?= t('Base date to calculate new due date: %s', $recurrence_basedate_list[$task['recurrence_basedate']]) ?><br/>
<?php if ($task['recurrence_parent']): ?>
    <?= t('Recurrent task created by: %s', $task['recurrence_parent']) ?><br/>
<?php endif ?>
<?php if ($task['recurrence_child']): ?>
    <?= t('Created recurrent task: %s', $task['recurrence_child']) ?><br/>
<?php endif ?>
</section>
