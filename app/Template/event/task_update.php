<?= $this->user->avatar($email, $author, 32) ?>

<p class="activity-title">
    <span class="activity-datetime">
        <?= $this->dt->datetime($date_creation) ?>
    </span>
    <i class="fa fa-pencil fa-fw"></i>
    <?= e('%s updated the task %s',
            $this->text->e($author),
            $this->url->link(t('#%d', $task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])).' <strong>'.$this->text->e($task['title']).'</strong>'
        ) ?>
</p>
<div class="activity-description">
    <?php if (isset($changes)): ?>
        <div class="activity-changes">
            <?= $this->render('task/changes', array('changes' => $changes, 'task' => $task)) ?>
        </div>
    <?php endif ?>
</div>
