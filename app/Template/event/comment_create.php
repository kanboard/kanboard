<p class="activity-title">
    <?= e('%s commented the task %s',
            $this->e($author),
            $this->a(t('#%d', $task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']))
        ) ?>
</p>
<div class="activity-description">
    <em><?= $this->e($task['title']) ?></em><br/>
    <div class="markdown"><?= $this->markdown($comment['comment']) ?></div>
</div>