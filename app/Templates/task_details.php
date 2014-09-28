<div class="task-<?= $task['color_id'] ?> task-show-details">
    <h2><?= Helper\escape($task['title']) ?></h2>
    <?php if ($task['score']): ?>
        <span class="task-score"><?= Helper\escape($task['score']) ?></span>
    <?php endif ?>
    <ul>
        <?php if ($task['reference']): ?>
        <li>
            <strong><?= t('Reference: %s', $task['reference']) ?></strong>
        </li>
        <?php endif ?>
        <li>
            <?= dt('Created on %B %e, %Y at %k:%M %p', $task['date_creation']) ?>
        </li>
        <?php if ($task['date_modification']): ?>
        <li>
            <?= dt('Last modified on %B %e, %Y at %k:%M %p', $task['date_modification']) ?>
        </li>
        <?php endif ?>
        <?php if ($task['date_completed']): ?>
        <li>
            <?= dt('Completed on %B %e, %Y at %k:%M %p', $task['date_completed']) ?>
        </li>
        <?php endif ?>
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
            (<?= Helper\escape($task['project_name']) ?>)
        </li>
        <li><?= t('Task position:').' '.Helper\escape($task['position']) ?></li>
        <?php if ($task['category_name']): ?>
        <li>
            <?= t('Category:') ?> <strong><?= Helper\escape($task['category_name']) ?></strong>
        </li>
        <?php endif ?>
        <li>
            <?php if ($task['is_active'] == 1): ?>
                <?= t('Status is open') ?>
            <?php else: ?>
                <?= t('Status is closed') ?>
            <?php endif ?>
        </li>
        <?php if ($project['is_public']): ?>
        <li>
            <a href="?controller=task&amp;action=readonly&amp;task_id=<?= $task['id'] ?>&amp;token=<?= $project['token'] ?>" target="_blank"><?= t('Public link') ?></a>
        </li>
        <?php endif ?>
    </ul>
</div>
