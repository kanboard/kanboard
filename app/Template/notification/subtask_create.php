<html>
<body>
<h2><?= $this->text->e($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<h3><?= t('New sub-task') ?></h3>

<ul>
    <li><?= t('Title:') ?> <?= $this->text->e($subtask['title']) ?></li>
    <li><?= t('Status:') ?> <?= t($subtask['status_name']) ?></li>
    <li><?= t('Assignee:') ?> <?= $this->text->e($subtask['name'] ?: $subtask['username'] ?: '?') ?></li>
    <?php if (! empty($subtask['time_estimated'])): ?>
        <li>
            <?= t('Time tracking:') ?>
            <?= t('%sh estimated', n($subtask['time_estimated'])) ?>
        </li>
    <?php endif ?>
</ul>

<?= $this->render('notification/footer', array('task' => $task)) ?>
</body>
</html>