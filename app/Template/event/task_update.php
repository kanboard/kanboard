<p class="activity-title">
    <?= e('%s updated the task %s',
            $this->text->e($author),
            $this->url->link(t('#%d', $task['id']), 'TaskViewController', 'show', array('task_id' => $task['id']))
        ) ?>
    <small class="activity-date"><?= $this->dt->datetime($date_creation) ?></small>
</p>
<div class="activity-description">
    <p class="activity-task-title"><?= $this->text->e($task['title']) ?></p>
    <?php if (isset($changes)): ?>
        <div class="activity-changes">
            <?= $this->render('task/changes', array('changes' => $changes, 'task' => $task)) ?>
        </div>
    <?php endif ?>
</div>
