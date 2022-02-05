<p class="activity-title">
    <?= e('%s set a new internal link for the task %s',
        $this->text->e($author),
        $this->url->link(t('#%d', $task['id']), 'TaskViewController', 'show', array('task_id' => $task['id']))
    ) ?>
    <small class="activity-date"><?= $this->dt->datetime($date_creation) ?></small>
</p>
<div class="activity-description">
    <p class="activity-task-title">
        <?= e('This task is now linked to the task %s with the relation "%s"',
              $this->url->link(t('#%d', $task_link['opposite_task_id']), 'TaskViewController', 'show', array('task_id' => $task_link['opposite_task_id'])),
              $this->text->e($task_link['label'])) ?>
    </p>
</div>
