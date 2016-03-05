<?= $this->user->avatar($email, $author) ?>

<p class="activity-title">
    <?= e('%s moved the task %s to the position #%d in the column "%s"',
            $this->text->e($author),
            $this->url->link(t('#%d', $task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])),
            $task['position'],
            $this->text->e($task['column_title'])
        ) ?>
</p>
<p class="activity-description">
    <em><?= $this->text->e($task['title']) ?></em>
</p>