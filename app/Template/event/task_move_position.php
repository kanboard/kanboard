<p class="activity-title">
    <?= e('%s moved the task %s to the position #%d in the column "%s"',
            $this->text->e($author),
            $this->url->link(t('#%d', $task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])),
            $task['position'],
            $this->text->e($task['column_title'])
        ) ?>
    <span class="activity-date"><?= $this->dt->datetime($date_creation) ?></span>
</p>
<div class="activity-description">
    <p class="activity-task-title"><?= $this->text->e($task['title']) ?></p>
</div>
