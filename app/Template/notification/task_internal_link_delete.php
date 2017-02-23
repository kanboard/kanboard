<h2><?= $this->text->e($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<p>
    <?= e(
        'The link with the relation "%s" to the task %s have been removed',
        $this->text->e($task_link['label']),
        $this->url->absoluteLink(t('#%d', $task_link['opposite_task_id']), 'TaskViewController', 'show', array('task_id' => $task_link['opposite_task_id']))
    ) ?>
</p>

<?= $this->render('notification/footer', array('task' => $task)) ?>
