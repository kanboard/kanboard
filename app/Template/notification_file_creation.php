<h2><?= Helper\escape($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<h3><?= t('New attachment added "%s"', $file['name']) ?></h3>

<?= Helper\template('notification_footer', array('task' => $task, 'application_url' => $application_url)) ?>