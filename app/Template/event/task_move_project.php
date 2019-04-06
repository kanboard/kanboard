<p class="activity-title">
    <?= e('%s moved the task #%d "%s" to the project "%s"',
            $this->text->e($author),
            $task['id'],
            $this->text->e($task['title']),
            $this->text->e($task['project_name'])
        ) ?>
    <small class="activity-date"><?= $this->dt->datetime($date_creation) ?></small>
</p>
<div class="activity-description">
    <p class="activity-task-title"><?= $this->text->e($task['title']) ?></p>
</div>
