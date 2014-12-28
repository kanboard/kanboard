<h2><?= $this->e($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<p><?= t('New attachment added "%s"', $file['name']) ?></p>

<?= $this->render('notification/footer', array('task' => $task, 'application_url' => $application_url)) ?>