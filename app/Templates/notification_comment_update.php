<h2><?= Helper\escape($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<h3><?= t('Comment updated') ?></h3>

<?= Helper\parse($comment['comment']) ?>

<hr/>
<p>Kanboard</p>