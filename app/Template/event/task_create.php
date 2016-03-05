<?= $this->user->avatar($email, $author) ?>

<p class="activity-title">
    <?= e('%s created the task %s',
            $this->text->e($author),
            $this->url->link(t('#%d', $task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']))
        ) ?>
</p>
<p class="activity-description">
    <em><?= $this->text->e($task['title']) ?></em>
</p>