<h2><?= t('Overdue tasks for the project "%s"', $project_name) ?></h2>

<ul>
    <?php foreach ($tasks as $task): ?>
        <li>
            (<strong>#<?= $task['id'] ?></strong>)
            <?php if ($application_url): ?>
                <a href="<?= $this->url->href('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, '', true) ?>"><?= $this->text->e($task['title']) ?></a>
            <?php else: ?>
                <?= $this->text->e($task['title']) ?>
            <?php endif ?>
            (<?= $this->dt->date($task['date_due']) ?>)
            <?php if ($task['assignee_username']): ?>
                (<strong><?= t('Assigned to %s', $task['assignee_name'] ?: $task['assignee_username']) ?></strong>)
            <?php endif ?>
        </li>
    <?php endforeach ?>
</ul>
