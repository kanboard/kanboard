<?= $this->user->avatar($email, $author, 32) ?>

<p class="activity-title">
    <?php $assignee = $task['assignee_name'] ?: $task['assignee_username'] ?>
    <i class="fa fa-user fa-fw"></i>
    <?php if (! empty($assignee)): ?>
        <?= e('%s changed the assignee of the task %s to %s',
                $this->text->e($author),
                $this->url->link(t('#%d', $task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])).' <strong>'.$this->text->e($task['title']).'</strong>',
                $this->text->e($assignee)
            ) ?>
    <?php else: ?>
        <?= e('%s remove the assignee of the task %s', $this->text->e($author), $this->url->link(t('#%d', $task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']))) ?>
    <?php endif ?>
    <span class="activity-datetime">
        <?= $this->dt->datetime($date_creation) ?>
    </span>
</p>
