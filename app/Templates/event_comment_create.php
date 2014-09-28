<p class="activity-title">
    <?= e('%s commented the task <a href="?controller=task&amp;action=show&amp;task_id=%d">#%d</a>', Helper\escape($author), $task_id, $task_id) ?>
</p>
<p class="activity-description">
    <em><?= Helper\escape($task_title) ?></em><br/>
    <div class="markdown"><?= Helper\markdown($comment) ?></div>
</p>