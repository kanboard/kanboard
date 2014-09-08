<?php if (isset($not_editable)): ?>

    <a href="?controller=task&amp;action=readonly&amp;task_id=<?= $task['id'] ?>&amp;token=<?= $project['token'] ?>">#<?= $task['id'] ?></a> -

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

    <a class="task-edit-popover" href="?controller=task&amp;action=edit&amp;task_id=<?= $task['id'] ?>" title="<?= t('Edit this task') ?>">#<?= $task['id'] ?></a> -

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


<?php if (! empty($task['date_due']) || ! empty($task['nb_files']) || ! empty($task['nb_comments']) || ! empty($task['description'])): ?>
<div class="task-board-footer">

    <?php if (! empty($task['date_due'])): ?>
    <div class="task-board-date <?= time() > $task['date_due'] ? 'task-board-date-overdue' : '' ?>">
        <?= dt('%B %e, %Y', $task['date_due']) ?>
    </div>
    <?php endif ?>

    <div class="task-board-icons">
        <?php if (! empty($task['nb_files'])): ?>
            <?= $task['nb_files'] ?> <i class="fa fa-paperclip" title="<?= t('Attachments') ?>"></i>
        <?php endif ?>

        <?php if (! empty($task['nb_comments'])): ?>
            <?= $task['nb_comments'] ?> <i class="fa fa-comment-o" title="<?= p($task['nb_comments'], t('%d comment', $task['nb_comments']), t('%d comments', $task['nb_comments'])) ?>"></i>
        <?php endif ?>

        <?php if (! empty($task['description'])): ?>
            <?php if (! isset($not_editable)): ?>
                <a class="task-description-popover" href="?controller=task&amp;action=description&amp;task_id=<?= $task['id'] ?>"><i class="fa fa-file-text-o" title="<?= t('Description') ?>" data-href="?controller=task&amp;action=description&amp;task_id=<?= $task['id'] ?>"></i></a>
            <?php else: ?>
                <i class="fa fa-file-text-o" title="<?= t('Description') ?>"></i>
            <?php endif ?>
        <?php endif ?>
    </div>
</div>
<?php endif ?>
