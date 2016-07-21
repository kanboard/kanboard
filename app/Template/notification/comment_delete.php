<h2><?= $this->text->e($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<h3><?= t('Comment removed') ?></h3>

<?= $this->text->markdown($comment['comment']) ?>

<?= $this->render('notification/footer', array('task' => $task, 'application_url' => $application_url)) ?>
