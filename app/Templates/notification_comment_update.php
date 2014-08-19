<h2><?= Helper\escape($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<h3><?= t('Comment updated') ?></h3>

<?= Helper\parse($comment['comment']) ?>

<?= Helper\template('notification_footer', array('task' => $task)) ?>