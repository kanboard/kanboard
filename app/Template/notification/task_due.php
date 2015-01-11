<h2><?= t('List of due tasks for the project "%s"', $project) ?></h2>

<ul>
    <?php foreach ($tasks as $task): ?>
        <li>
            (<strong>#<?= $task['id'] ?></strong>)
            <?php if ($application_url): ?>
                <a href="<?= $application_url.$this->u('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><?= $this->e($task['title']) ?></a>
            <?php else: ?>
                <?= $this->e($task['title']) ?>
            <?php endif ?>
            (<?= dt('%B %e, %Y', $task['date_due']) ?>)
            <?php if ($task['assignee_username']): ?>
                (<strong><?= t('Assigned to %s', $task['assignee_name'] ?: $task['assignee_username']) ?></strong>)
            <?php endif ?>
        </li>
    <?php endforeach ?>
</ul>

<?= $this->render('notification/footer', array('task' => $task)) ?>
