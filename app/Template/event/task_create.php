<p class="activity-title">
    <?= e('%s created the task <a href="?controller=task&amp;action=show&amp;task_id=%d">#%d</a>', $this->e($author), $task_id, $task_id) ?>
</p>
<p class="activity-description">
    <em><?= $this->e($task['title']) ?></em>
</p>