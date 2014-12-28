<h2><?= $this->e($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<h3><?= t('New comment posted by %s', $comment['name'] ?: $comment['username']) ?></h3>

<?= $this->markdown($comment['comment']) ?>

<?= $this->render('notification/footer', array('task' => $task, 'application_url' => $application_url)) ?>