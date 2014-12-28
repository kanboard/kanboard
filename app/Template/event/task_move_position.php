<p class="activity-title">
    <?= e('%s moved the task <a href="?controller=task&amp;action=show&amp;task_id=%d">#%d</a> to the position #%d in the column "%s"', $this->e($author), $task_id, $task_id, $task['position'], $this->e($task['column_title'])) ?>
</p>
<p class="activity-description">
    <em><?= $this->e($task['title']) ?></em>
</p>