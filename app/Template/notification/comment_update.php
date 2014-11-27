<h2><?= Helper\escape($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<h3><?= t('Comment updated') ?></h3>

<?= Helper\markdown($comment['comment']) ?>

<?= Helper\template('notification/footer', array('task' => $task, 'application_url' => $application_url)) ?>