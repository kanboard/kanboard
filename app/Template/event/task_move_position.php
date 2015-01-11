<p class="activity-title">
    <?= e('%s moved the task %s to the position #%d in the column "%s"',
            $this->e($author),
            $this->a(t('#%d', $task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])),
            $task['position'],
            $this->e($task['column_title'])
        ) ?>
</p>
<p class="activity-description">
    <em><?= $this->e($task['title']) ?></em>
</p>