<h2><?= $this->e($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<?= $this->render('task/changes', array('changes' => $changes, 'task' => $task)) ?>
<?= $this->render('notification/footer', array('task' => $task, 'application_url' => $application_url)) ?>