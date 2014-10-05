<h2><?= t('List of due tasks for the project "%s"', $project) ?></h2>

<ul>
    <?php foreach ($tasks as $task): ?>
        <li>
            (<strong>#<?= $task['id'] ?></strong>)
            <?= Helper\escape($task['title']) ?>
            <?php if ($task['assignee_username']): ?>
                (<strong><?= t('Assigned to %s', $task['assignee_name'] ?: $task['assignee_username']) ?></strong>)
            <?php endif ?>
        </li>
    <?php endforeach ?>
</ul>

<?= Helper\template('notification_footer', array('task' => $task, 'application_url' => $application_url)) ?>