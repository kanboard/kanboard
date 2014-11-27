<h2><?= Helper\escape($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<p><?= t('New attachment added "%s"', $file['name']) ?></p>

<?= Helper\template('notification/footer', array('task' => $task, 'application_url' => $application_url)) ?>