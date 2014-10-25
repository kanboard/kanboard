<?php if (isset($not_editable)): ?>

    <a href="?controller=task&amp;action=readonly&amp;task_id=<?= $task['id'] ?>&amp;token=<?= $project['token'] ?>">#<?= $task['id'] ?></a>

    <?php if ($task['reference']): ?>
    <span class="task-board-reference" title="<?= t('Reference') ?>">
        (<?= $task['reference'] ?>)
    </span>
    <?php endif ?>

    &nbsp;-&nbsp;

    <span class="task-board-user">
    <?php if (! empty($task['owner_id'])): ?>
        <?= t('Assigned to %s', $task['assignee_name'] ?: $task['assignee_username']) ?>
    <?php else: ?>
        <span class="task-board-nobody"><?= t('Nobody assigned') ?></span>
    <?php endif ?>
    </span>

    <?php if ($task['score']): ?>
        <span class="task-score"><?= Helper\escape($task['score']) ?></span>
    <?php endif ?>

    <div class="task-board-title">
        <a href="?controller=task&amp;action=readonly&amp;task_id=<?= $task['id'] ?>&amp;token=<?= $project['token'] ?>">
            <?= Helper\escape($task['title']) ?>
        </a>
    </div>

<?php else: ?>

    <a class="task-edit-popover" href="?controller=task&amp;action=edit&amp;task_id=<?= $task['id'] ?>" title="<?= t('Edit this task') ?>">#<?= $task['id'] ?></a>

    <?php if ($task['reference']): ?>
    <span class="task-board-reference" title="<?= t('Reference') ?>">
        (<?= $task['reference'] ?>)
    </span>
    <?php endif ?>

    &nbsp;-&nbsp;

    <span class="task-board-user">
        <a class="assignee-popover" href="?controller=board&amp;action=changeAssignee&amp;task_id=<?= $task['id'] ?>" title="<?= t('Change assignee') ?>">
        <?php if (! empty($task['owner_id'])): ?>
            <?= t('Assigned to %s', $task['assignee_name'] ?: $task['assignee_username']) ?></a>
        <?php else: ?>
            <?= t('Nobody assigned') ?>
        <?php endif ?>
        </a>
    </span>

    <?php if ($task['score']): ?>
        <span class="task-score"><?= Helper\escape($task['score']) ?></span>
    <?php endif ?>

    <div class="task-board-title">
        <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>" title="<?= t('View this task') ?>"><?= Helper\escape($task['title']) ?></a>
    </div>

<?php endif ?>


<?php if ($task['category_id']): ?>
<div class="task-board-category-container">
    <span class="task-board-category">
        <a class="category-popover" href="?controller=board&amp;action=changeCategory&amp;task_id=<?= $task['id'] ?>" title="<?= t('Change category') ?>">
            <?= Helper\in_list($task['category_id'], $categories) ?>
        </a>
    </span>
</div>
<?php endif ?>


<?php if (! empty($task['date_due']) || ! empty($task['nb_files']) || ! empty($task['nb_comments']) || ! empty($task['description']) || ! empty($task['nb_subtasks'])): ?>
<div class="task-board-footer">

    <?php if (! empty($task['date_due'])): ?>
    <div class="task-board-date <?= time() > $task['date_due'] ? 'task-board-date-overdue' : '' ?>">
        <?= dt('%B %e, %Y', $task['date_due']) ?>
    </div>
    <?php endif ?>

    <div class="task-board-icons">

        <?php if (! empty($task['nb_subtasks'])): ?>
            <span title="<?= t('Sub-Tasks') ?>"><?= $task['nb_completed_subtasks'].'/'.$task['nb_subtasks'] ?> <i class="fa fa-bars"></i></span>
        <?php endif ?>

        <?php if (! empty($task['nb_files'])): ?>
            <span title="<?= t('Attachments') ?>"><?= $task['nb_files'] ?> <i class="fa fa-paperclip"></i></span>
        <?php endif ?>

        <?php if (! empty($task['nb_comments'])): ?>
            <span title="<?= p($task['nb_comments'], t('%d comment', $task['nb_comments']), t('%d comments', $task['nb_comments'])) ?>"><?= $task['nb_comments'] ?> <i class="fa fa-comment-o"></i></span>
        <?php endif ?>

        <?php if (! empty($task['description'])): ?>
            <span title="<?= t('Description') ?>">
            <?php if (! isset($not_editable)): ?>
                <a class="task-description-popover" href="?controller=task&amp;action=description&amp;task_id=<?= $task['id'] ?>"><i class="fa fa-file-text-o" data-href="?controller=task&amp;action=description&amp;task_id=<?= $task['id'] ?>"></i></a>
            <?php else: ?>
                <i class="fa fa-file-text-o"></i>
            <?php endif ?>
            </span>
        <?php endif ?>
    </div>
</div>
<?php endif ?>
