<p class="activity-title">
    <?= e(
            '%s change the assignee of the task <a href="?controller=task&amp;action=show&amp;task_id=%d">#%d</a> to %s',
            $this->e($author),
            $task_id,
            $task_id,
            $this->e($task['assignee_name'] ?: $task['assignee_username'])
    ) ?>
</p>
<p class="activity-description">
    <em><?= $this->e($task['title']) ?></em>
</p>