<p class="activity-title">
    <?= e('%s commented the task <a href="?controller=task&amp;action=show&amp;task_id=%d">#%d</a>', $this->e($author), $task_id, $task_id) ?>
</p>
<div class="activity-description">
    <em><?= $this->e($task['title']) ?></em><br/>
    <div class="markdown"><?= $this->markdown($comment['comment']) ?></div>
</div>