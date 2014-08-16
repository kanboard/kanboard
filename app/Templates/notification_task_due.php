<h2><?= t('List of due tasks for the project "%s"', $project) ?></h2>

<ul>
    <?php foreach ($tasks as $task): ?>
        <li>(<strong>#<?= $task['id'] ?></strong>) <?= Helper\escape($task['title']) ?> (<strong><?= t('Assigned to %s', $task['assignee_name'] ?: $task['assignee_username']) ?></strong>)</li>
    <?php endforeach ?>
</ul>

<hr/>
<p>Kanboard</p>