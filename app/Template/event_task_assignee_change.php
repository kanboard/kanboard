<p class="activity-title">
    <?= e(
            '%s change the assignee of the task <a href="?controller=task&amp;action=show&amp;task_id=%d">#%d</a> to %s',
            Helper\escape($author),
            $task_id,
            $task_id,
            Helper\escape($task['assignee_name'] ?: $task['assignee_username'])
    ) ?>
</p>
<p class="activity-description">
    <em><?= Helper\escape($task['title']) ?></em>
</p>