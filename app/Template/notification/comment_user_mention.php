<h2><?= t('You were mentioned in a comment on the task #%d', $task['id']) ?></h2>

<p><?= $this->text->e($task['title']) ?></p>

<?= $this->text->markdown($comment['comment'], true) ?>

<?= $this->render('notification/footer', array('task' => $task)) ?>