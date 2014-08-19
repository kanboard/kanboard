<h2><?= Helper\escape($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<ul>
    <li>
        <?= dt('Created on %B %e, %Y at %k:%M %p', $task['date_creation']) ?>
    </li>
    <?php if ($task['date_due']): ?>
    <li>
        <strong><?= dt('Must be done before %B %e, %Y', $task['date_due']) ?></strong>
    </li>
    <?php endif ?>
    <?php if ($task['creator_username']): ?>
    <li>
        <?= t('Created by %s', $task['creator_name'] ?: $task['creator_username']) ?>
    </li>
    <?php endif ?>
    <li>
        <strong>
        <?php if ($task['assignee_username']): ?>
            <?= t('Assigned to %s', $task['assignee_name'] ?: $task['assignee_username']) ?>
        <?php else: ?>
            <?= t('There is nobody assigned') ?>
        <?php endif ?>
        </strong>
    </li>
    <li>
        <?= t('Column on the board:') ?>
        <strong><?= Helper\escape($task['column_title']) ?></strong>
    </li>
    <li><?= t('Task position:').' '.Helper\escape($task['position']) ?></li>
    <?php if ($task['category_name']): ?>
    <li>
        <?= t('Category:') ?> <strong><?= Helper\escape($task['category_name']) ?></strong>
    </li>
    <?php endif ?>
</ul>

<?php if (! empty($task['description'])): ?>
    <h2><?= t('Description') ?></h2>
    <?= Helper\parse($task['description']) ?>
<?php endif ?>

<?= Helper\template('notification_footer', array('task' => $task)) ?>