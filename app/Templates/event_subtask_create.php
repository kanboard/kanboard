<p class="activity-title">
    <?= e('%s created a subtask for the task <a href="?controller=task&amp;action=show&amp;task_id=%d">#%d</a>', Helper\escape($author), $task_id, $task_id) ?>
</p>
<p class="activity-description">
    <em><?= Helper\escape($task_title) ?></em><br/>
    <p><?= Helper\escape($subtask_title) ?> <strong>(<?= Helper\in_list($subtask_status, $subtask_status_list) ?>)</strong></p>
    <?php if ($subtask_assignee): ?>
        <p><?= t('Assigned to %s with an estimate of %s/%sh', $subtask_assignee, $subtask_time_spent, $subtask_time_estimated) ?></p>
    <?php else: ?>
        <p><?= t('Not assigned, estimate of %sh', $subtask_time_estimated) ?></p>
    <?php endif ?>
</p>