<?= $this->user->avatar($email, $author, 32) ?>

<p class="activity-title">
    <i class="fa fa-arrows-v fa-fw"></i>
    <?php if ($task['swimlane_id'] == 0): ?>
        <?= e('%s moved the task %s to the first swimlane',
                $this->text->e($author),
                $this->url->link(t('#%d', $task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])).' <strong>'.$this->text->e($task['title']).'</strong>'
            ) ?>
    <?php else: ?>
        <?= e('%s moved the task %s to the swimlane "%s"',
                $this->text->e($author),
                $this->url->link(t('#%d', $task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']))' <strong>'.$task['title'].'</strong>',
                $this->text->e($task['swimlane_name'])
            ) ?>
    <?php endif ?>
    <span class="activity-datetime">
        <?= $this->dt->datetime($date_creation) ?>
    </span>
</p>
