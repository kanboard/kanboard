<?= $this->user->avatar($email, $author, 32) ?>
<p class="activity-title">
    <i class="fa fa-tasks fa-fw"></i>
    <?= e('%s created a subtask for the task %s',
            $this->text->e($author),
            $this->url->link(t('#%d', $task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])).' <strong>'.$this->text->e($task['title']).'</strong>'
        ) ?>
    <span class="activity-datetime">
        <?= $this->dt->datetime($date_creation) ?>
    </span>
</p>
<div class="activity-description">
    <ul>
        <li>
            <?= $this->text->e($subtask['title']) ?>
        </li>
        <li>
            <?php if ($subtask['username']): ?>
                <?= t('Assigned to %s with an estimate of %s/%sh', $subtask['name'] ?: $subtask['username'], $subtask['time_spent'], $subtask['time_estimated']) ?>
            <?php else: ?>
                <?= t('Not assigned, estimate of %sh', $subtask['time_estimated']) ?>
            <?php endif ?>
        </li>
    </ul>
</div>
