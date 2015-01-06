<h2><?= $this->e($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<ul>
    <li>
        <strong>
        <?php if ($task['assignee_username']): ?>
            <?= t('Assigned to %s', $task['assignee_name'] ?: $task['assignee_username']) ?>
        <?php else: ?>
            <?= t('There is nobody assigned') ?>
        <?php endif ?>
        </strong>
    </li>
</ul>

<?php if (! empty($task['description'])): ?>
    <h2><?= t('Description') ?></h2>
    <?= $this->markdown($task['description']) ?: t('There is no description.') ?>
<?php endif ?>

<?= $this->render('notification/footer', array('task' => $task, 'application_url' => $application_url)) ?>