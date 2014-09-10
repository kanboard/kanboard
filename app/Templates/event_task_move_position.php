<p class="activity-title">
    <?= e('%s moved the task <a href="?controller=task&amp;action=show&amp;task_id=%d">#%d</a> to the position #%d in the column "%s"', Helper\escape($author), $task_id, $task_id, $task_position, Helper\escape($task_column_name)) ?>
</p>
<p class="activity-description">
    <em><?= Helper\escape($task_title) ?></em>
</p>