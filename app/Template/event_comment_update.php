<p class="activity-title">
    <?= e('%s updated a comment on the task <a href="?controller=task&amp;action=show&amp;task_id=%d">#%d</a>', Helper\escape($author), $task_id, $task_id) ?>
</p>
<div class="activity-description">
    <em><?= Helper\escape($task['title']) ?></em><br/>
</div>