<p class="activity-title">
    <?php $assignee = $task['assignee_name'] ?: $task['assignee_username'] ?>

    <?php if (! empty($assignee)): ?>
        <?= e('%s changed the assignee of the task %s to %s',
                $this->text->e($author),
                $this->url->link(t('#%d', $task['id']), 'TaskViewController', 'show', array('task_id' => $task['id'])),
                $this->text->e($assignee)
            ) ?>
    <?php else: ?>
        <?= e('%s removed the assignee of the task %s', $this->text->e($author), $this->url->link(t('#%d', $task['id']), 'TaskViewController', 'show', array('task_id' => $task['id']))) ?>
    <?php endif ?>
    <small class="activity-date"><?= $this->dt->datetime($date_creation) ?></small>
</p>
<div class="activity-description">
    <p class="activity-task-title"><?= $this->text->e($task['title']) ?></p>
</div>
