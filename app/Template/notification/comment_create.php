<h2><?= $this->e($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<?php if (! empty($comment['username'])): ?>
    <h3><?= t('New comment posted by %s', $comment['name'] ?: $comment['username']) ?></h3>
<?php else: ?>
    <h3><?= t('New comment') ?></h3>
<?php endif ?>

<?= $this->text->markdown($comment['comment']) ?>

<?= $this->render('notification/footer', array('task' => $task, 'application_url' => $application_url)) ?>