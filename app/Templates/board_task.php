<?php if (isset($not_editable)): ?>

    #<?= $task['id'] ?> -

    <span class="task-board-user">
    <?php if (! empty($task['owner_id'])): ?>
        <?= t('Assigned to %s', $task['username']) ?>
    <?php else: ?>
        <span class="task-board-nobody"><?= t('Nobody assigned') ?></span>
    <?php endif ?>
    </span>

    <?php if ($task['score']): ?>
        <span class="task-score"><?= Helper\escape($task['score']) ?></span>
    <?php endif ?>

    <div class="task-board-title">
        <?= Helper\escape($task['title']) ?>
    </div>

<?php else: ?>

    <a class="task-edit-popover" href="?controller=task&amp;action=edit&amp;task_id=<?= $task['id'] ?>" title="<?= t('Edit this task') ?>">#<?= $task['id'] ?></a> -

    <span class="task-board-user">
    <?php if (! empty($task['owner_id'])): ?>
        <a class="assignee-popover" href="?controller=board&amp;action=assign&amp;task_id=<?= $task['id'] ?>" title="<?= t('Change assignee') ?>"><?= t('Assigned to %s', $task['username']) ?></a>
    <?php else: ?>
        <a class="assignee-popover" href="?controller=board&amp;action=assign&amp;task_id=<?= $task['id'] ?>" title="<?= t('Change assignee') ?>" class="task-board-nobody"><?= t('Nobody assigned') ?></a>
    <?php endif ?>
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
        <?= Helper\in_list($task['category_id'], $categories) ?>
    </span>
</div>
<?php endif ?>


<?php if (! empty($task['date_due']) || ! empty($task['nb_files']) || ! empty($task['nb_comments']) || ! empty($task['description'])): ?>
<div class="task-board-footer">

    <?php if (! empty($task['date_due'])): ?>
    <div class="task-board-date">
        <?= dt('%B %e, %G', $task['date_due']) ?>
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
            <a class="task-board-popover" href='?controller=task&amp;action=editDescription&amp;task_id=<?= $task['id'] ?>'><i class="fa fa-file-text-o" title="<?= t('Description') ?>"></i></a>
        <?php endif ?>
    </div>
</div>
<?php endif ?>
