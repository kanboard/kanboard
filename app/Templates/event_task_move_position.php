<p class="activity-title">
    <?= t('%s moved the task #%d to the position %d in the column %s', $author, $task_id, $task_position, $task_column_name) ?>
</p>
<p class="activity-description">
    <em><?= Helper\escape($task_title) ?></em>
</p>