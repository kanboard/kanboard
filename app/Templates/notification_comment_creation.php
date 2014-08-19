<h2><?= Helper\escape($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<h3><?= t('New comment posted by %s', $comment['name'] ?: $comment['username']) ?></h3>

<?= Helper\parse($comment['comment']) ?>

<?= Helper\template('notification_footer', array('task' => $task)) ?>