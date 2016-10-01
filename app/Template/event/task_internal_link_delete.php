<p class="activity-title">
    <?= e('%s removed an internal link for the task %s',
        $this->text->e($author),
        $this->url->link(t('#%d', $task['id']), 'TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']))
    ) ?>
    <small class="activity-date"><?= $this->dt->datetime($date_creation) ?></small>
</p>
<div class="activity-description">
    <p class="activity-task-title">
        <?= e(
            'The link with the relation "%s" to the task %s have been removed',
            $this->text->e($task_link['label']),
            $this->url->link(t('#%d', $task_link['opposite_task_id']), 'TaskViewController', 'show', array('task_id' => $task_link['opposite_task_id']))
        ) ?>
    </p>
</div>
