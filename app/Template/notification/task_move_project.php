<html>
<body>
<h2><?= $this->text->e($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<p><?= t('Task #%d "%s" has been moved to the project "%s"', $task['id'], $task['title'], $task['project_name']) ?></p>

<?= $this->render('notification/footer', array('task' => $task)) ?>
</body>
</html>