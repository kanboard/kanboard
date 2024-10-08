<p class="activity-title">
    <?php if ($task['swimlane_id'] == 0): ?>
        <?= e('%s moved the task %s to the first swimlane',
                $this->text->e($author),
                $this->url->link(t('#%d', $task['id']), 'TaskViewController', 'show', array('task_id' => $task['id']))
            ) ?>
    <?php else: ?>
        <?= e('%s moved the task %s to the swimlane "%s"',
                $this->text->e($author),
                $this->url->link(t('#%d', $task['id']), 'TaskViewController', 'show', array('task_id' => $task['id'])),
                $this->text->e($task['swimlane_name'])
            ) ?>
    <?php endif ?>
    <small class="activity-date"><?= $this->dt->datetime($date_creation) ?></small>
</p>
<div class="activity-description">
    <p class="activity-task-title"><?= $this->text->e($task['title']) ?></p>
</div>
