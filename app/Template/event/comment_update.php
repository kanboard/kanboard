<p class="activity-title">
    <?= e('%s updated a comment on the task %s',
            $this->text->e($author),
            $this->url->link(t('#%d', $task['id']), 'TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']))
        ) ?>
    <small class="activity-date"><?= $this->dt->datetime($date_creation) ?></small>
</p>
<div class="activity-description">
    <p class="activity-task-title"><?= $this->text->e($task['title']) ?></p>
    <?php if (! empty($comment['comment'])): ?>
        <div class="markdown"><?= $this->text->markdown($comment['comment']) ?></div>
    <?php endif ?>
</div>
