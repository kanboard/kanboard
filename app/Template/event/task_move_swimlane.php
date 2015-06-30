<?= $this->user->avatar($email, $author) ?>

<p class="activity-title">
    <?php if ($task['swimlane_id'] == 0): ?>
        <?= e('%s moved the task %s to the first swimlane',
                $this->e($author),
                $this->url->link(t('#%d', $task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']))
            ) ?>
    <?php else: ?>
        <?= e('%s moved the task %s to the swimlane "%s"',
                $this->e($author),
                $this->url->link(t('#%d', $task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])),
                $this->e($task['swimlane_name'])
            ) ?>
    <?php endif ?>
</p>
<p class="activity-description">
    <em><?= $this->e($task['title']) ?></em>
</p>