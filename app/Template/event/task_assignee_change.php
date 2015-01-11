<p class="activity-title">
    <?= e('%s changed the assignee of the task %s to %s',
            $this->e($author),
            $this->a(t('#%d', $task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])),
            $this->e($task['assignee_name'] ?: $task['assignee_username'])
        ) ?>
</p>
<p class="activity-description">
    <em><?= $this->e($task['title']) ?></em>
</p>