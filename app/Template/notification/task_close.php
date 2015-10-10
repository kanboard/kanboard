<h2><?= $this->e($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<p><?= t('The task #%d have been closed.', $task['id']) ?></p>

<?= $this->render('notification/footer', array('task' => $task, 'application_url' => $application_url)) ?>