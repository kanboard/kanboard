<html>
<body>
<h2><?= $this->text->e($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<h3><?= t('Subtask removed') ?></h3>

<ul>
    <li><?= t('Title:') ?> <?= $this->text->e($subtask['title']) ?></li>
    <li><?= t('Status:') ?> <?= t($subtask['status_name']) ?></li>
    <li><?= t('Assignee:') ?> <?= $this->text->e($subtask['name'] ?: $subtask['username'] ?: '?') ?></li>
</ul>

<?= $this->render('notification/footer', array('task' => $task)) ?>
</body>
</html>