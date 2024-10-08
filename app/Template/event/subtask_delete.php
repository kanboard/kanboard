<p class="activity-title">
    <?= e('%s removed a subtask for the task %s',
        $this->text->e($author),
        $this->url->link(t('#%d', $task['id']), 'TaskViewController', 'show', array('task_id' => $task['id']))
    ) ?>
    <small class="activity-date"><?= $this->dt->datetime($date_creation) ?></small>
</p>
<div class="activity-description">
    <p class="activity-task-title"><?= $this->text->e($task['title']) ?></p>
    <ul>
        <li>
            <?= $this->text->e($subtask['title']) ?> (<strong><?= $this->text->e($subtask['status_name']) ?></strong>)
        </li>
    </ul>
</div>
