<?= $this->user->avatar($email, $author, 32) ?>

<p class="activity-title">
    <span class="activity-datetime">
        <?= $this->dt->datetime($date_creation) ?>
    </span>
    <i class="fa fa-arrows-v fa-fw"></i>
    <?= e('%s moved the task %s to the position #%d in the column "%s"',
            $this->text->e($author),
            $this->url->link(t('#%d', $task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])).' <strong>'.$this->text->e($task['title']).'</strong>',
            $task['position'],
            $this->text->e($task['column_title'])
        ) ?>
</p>
