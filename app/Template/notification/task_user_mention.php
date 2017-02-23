<h2><?= t('You were mentioned in the task #%d', $task['id']) ?></h2>
<p><?= $this->text->e($task['title']) ?></p>

<h2><?= t('Description') ?></h2>
<?= $this->text->markdown($task['description'], true) ?>

<?= $this->render('notification/footer', array('task' => $task)) ?>