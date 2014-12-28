<p class="activity-title">
    <?= e('%s created a subtask for the task <a href="?controller=task&amp;action=show&amp;task_id=%d">#%d</a>', $this->e($author), $task_id, $task_id) ?>
</p>
<div class="activity-description">
    <p><em><?= $this->e($task['title']) ?></em></p>

    <ul>
        <li>
            <?= $this->e($subtask['title']) ?> (<strong><?= $this->e($subtask['status_name']) ?></strong>)
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