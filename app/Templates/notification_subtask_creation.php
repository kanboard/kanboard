<h2><?= Helper\escape($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<h3><?= t('New sub-task') ?></h3>

<ul>
    <li><?= t('Title:') ?> <?= Helper\escape($subtask['title']) ?></li>
    <li><?= t('Status:') ?> <?= Helper\escape($subtask['status_name']) ?></li>
    <li><?= t('Assignee:') ?> <?= Helper\escape($subtask['name'] ?: $subtask['username'] ?: '?') ?></li>
    <li>
        <?= t('Time tracking:') ?>
        <?php if (! empty($subtask['time_estimated'])): ?>
            <strong><?= Helper\escape($subtask['time_estimated']).'h' ?></strong> <?= t('estimated') ?>
        <?php endif ?>
    </li>
</ul>

<?= Helper\template('notification_footer', array('task' => $task)) ?>