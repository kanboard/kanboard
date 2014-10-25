<p class="activity-title">
    <?= e('%s updated a subtask for the task <a href="?controller=task&amp;action=show&amp;task_id=%d">#%d</a>', Helper\escape($author), $task_id, $task_id) ?>
</p>
<div class="activity-description">
    <p><em><?= Helper\escape($task['title']) ?></em></p>

    <ul>
        <li>
            <?= Helper\escape($subtask['title']) ?> (<strong><?= Helper\escape($subtask['status_name']) ?></strong>)
        </li>
        <li>
            <?php if ($subtask['username']): ?>
                <?= t('Assigned to %s with an estimate of %s/%sh', $subtask['name'] ?: $subtask['username'], $subtask['time_spent'], $subtask['time_estimated']) ?>
            <?php else: ?>
                <?= t('Not assigned, estimate of %sh', $subtask['time_estimated']) ?>
            <?php endif ?>
        </li>
    </ul>
</div>
