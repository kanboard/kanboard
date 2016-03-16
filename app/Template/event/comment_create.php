<?= $this->user->avatar($email, $author, 32) ?>

<p class="activity-title">
<span class="activity-datetime">
    <?= $this->dt->datetime($date_creation) ?>
</span>
    <i class="fa fa-comment-o fa-fw"></i>
    <?= e('%s commented the task %s',
            $this->text->e($author),
            $this->url->link(t('#%d', $task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])).' <strong>'.$this->text->e($task['title']).'</strong>'
        ) ?>
</p>
<div class="activity-description">
    <div class="markdown"><?= $this->text->markdown($comment['comment']) ?></div>
</div>
