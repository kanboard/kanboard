<h2><?= $this->e($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<h3><?= t('Comment updated') ?></h3>

<?= $this->markdown($comment['comment']) ?>

<?= $this->render('notification/footer', array('task' => $task, 'application_url' => $application_url)) ?>