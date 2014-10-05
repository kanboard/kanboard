<h2><?= Helper\escape($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<p><?= t('The task #%d have been closed.', $task['id']) ?></p>

<?= Helper\template('notification_footer', array('task' => $task, 'application_url' => $application_url)) ?>