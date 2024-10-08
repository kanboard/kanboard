<p class="activity-title">
    <?= e('%s moved the task %s to the position #%d in the column "%s"',
            $this->text->e($author),
            $this->url->link(t('#%d', $task['id']), 'TaskViewController', 'show', array('task_id' => $task['id'])),
            $task['position'],
            $this->text->e($task['column_title'])
        ) ?>
    <small class="activity-date"><?= $this->dt->datetime($date_creation) ?></small>
</p>
<div class="activity-description">
    <p class="activity-task-title"><?= $this->text->e($task['title']) ?></p>
</div>
