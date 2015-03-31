<?= $this->avatar($email, $author) ?>

<p class="activity-title">
    <?php $assignee = $task['assignee_name'] ?: $task['assignee_username'] ?>

    <?php if (! empty($assignee)): ?>
        <?= e('%s changed the assignee of the task %s to %s',
                $this->e($author),
                $this->a(t('#%d', $task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])),
                $this->e($assignee)
            ) ?>
    <?php else: ?>
        <?= e('%s remove the assignee of the task %s', $this->e($author), $this->a(t('#%d', $task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']))) ?>
    <?php endif ?>
</p>
<p class="activity-description">
    <em><?= $this->e($task['title']) ?></em>
</p>