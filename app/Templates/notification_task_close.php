<h2><?= Helper\escape($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<p><?= t('The task #%d have been closed.', $task['id']) ?></p>

<hr/>
<p>Kanboard</p>