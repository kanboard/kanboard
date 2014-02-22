<section id="main">
    <div class="page-header">
        <h2>#<?= $task['id'] ?> - <?= Helper\escape($task['title']) ?></h2>
        <ul>
            <li><a href="?controller=board&amp;action=show&amp;project_id=<?= $task['project_id'] ?>"><?= t('Back to the board') ?></a></li>
        </ul>
    </div>
    <section>
        <h3><?= t('Details') ?></h3>
        <article id="infos" class="task task-<?= $task['color_id'] ?>">
        <ul>
            <li>
                <?= dt('Created on %B %e, %G at %k:%M %p', $task['date_creation']) ?>
            </li>
            <?php if ($task['date_completed']): ?>
            <li>
                <?= dt('Completed on %B %e, %G at %k:%M %p', $task['date_completed']) ?>
            </li>
            <?php endif ?>
            <li>
                <strong>
                <?php if ($task['username']): ?>
                    <?= t('Assigned to %s', $task['username']) ?>
                <?php else: ?>
                    <?= t('There is no body assigned') ?>
                <?php endif ?>
                </strong>
            </li>
            <li>
                <?= t('Column on the board:') ?>
                <strong><?= Helper\escape($task['column_title']) ?></strong>
                (<?= Helper\escape($task['project_name']) ?>)
            </li>
            <li>
                <?php if ($task['is_active'] == 1): ?>
                    <?= t('Status is open') ?>
                <?php else: ?>
                    <?= t('Status is closed') ?>
                <?php endif ?>
            </li>
            <li>
                <a href="?controller=task&amp;action=edit&amp;task_id=<?= $task['id'] ?>"><?= t('Edit') ?></a>
                <?= t('or') ?>
                <?php if ($task['is_active'] == 1): ?>
                    <a href="?controller=task&amp;action=confirmClose&amp;task_id=<?= $task['id'] ?>"><?= t('close this task') ?></a>
                <?php else: ?>
                    <a href="?controller=task&amp;action=confirmOpen&amp;task_id=<?= $task['id'] ?>"><?= t('open this task') ?></a>
                <?php endif ?>
            </li>
        </ul>
        </article>

        <?php if ($task['description']): ?>
            <h3><?= t('Description') ?></h3>
            <article id="description">
                <?= Helper\markdown($task['description']) ?: t('There is no description.') ?>
            </article>
        <?php endif ?>

    </section>
</section>