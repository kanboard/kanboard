<?= $this->user->avatar($email, $author) ?>

<p class="activity-title">
    <?= e('%s updated the task %s',
            $this->e($author),
            $this->url->link(t('#%d', $task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']))
        ) ?>
</p>
<p class="activity-description">
    <em><?= $this->e($task['title']) ?></em>
    <?php if (isset($changes)): ?>
        <div class="activity-changes">
            <?= $this->render('task/changes', array('changes' => $changes, 'task' => $task)) ?>
        </div>
    <?php endif ?>
</p>