<p class="activity-title">
    <?= e('%s moved the task <a href="?controller=task&amp;action=show&amp;task_id=%d">#%d</a> to the column "%s"', Helper\escape($author), $task_id, $task_id, Helper\escape($task['column_title'])) ?>
</p>
<p class="activity-description">
    <em><?= Helper\escape($task['title']) ?></em>
</p>