<h2><?= Helper\escape($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<ul>
    <li>
        <?= t('Column on the board:') ?>
        <strong><?= Helper\escape($task['column_title']) ?></strong>
    </li>
    <li><?= t('Task position:').' '.Helper\escape($task['position']) ?></li>
</ul>

<?= Helper\template('notification/footer', array('task' => $task, 'application_url' => $application_url)) ?>