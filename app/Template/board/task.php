<?php if (isset($not_editable)): ?>

    <?= Helper\a('#'.$task['id'], 'task', 'readonly', array('task_id' => $task['id'], 'token' => $project['token'])) ?>

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
        <?= Helper\a(Helper\escape($task['title']), 'task', 'readonly', array('task_id' => $task['id'], 'token' => $project['token'])) ?>
    </div>

<?php else: ?>

    <?= Helper\a('#'.$task['id'], 'task', 'edit', array('task_id' => $task['id']), false, 'task-edit-popover', t('Edit this task')) ?>

    <?php if ($task['reference']): ?>
    <span class="task-board-reference" title="<?= t('Reference') ?>">
        (<?= $task['reference'] ?>)
    </span>
    <?php endif ?>

    &nbsp;-&nbsp;

    <span class="task-board-user">
        <?= Helper\a(
            ! empty($task['owner_id']) ? t('Assigned to %s', $task['assignee_name'] ?: $task['assignee_username']) : t('Nobody assigned'),
            'board',
            'changeAssignee',
            array('task_id' => $task['id']),
            false,
            'assignee-popover',
            t('Change assignee')
        ) ?>
    </span>

    <?php if ($task['score']): ?>
        <span class="task-score"><?= Helper\escape($task['score']) ?></span>
    <?php endif ?>

    <div class="task-board-title">
        <?= Helper\a(Helper\escape($task['title']), 'task', 'show', array('task_id' => $task['id']), false, '', t('View this task')) ?>
    </div>

<?php endif ?>


<?php if ($task['category_id']): ?>
<div class="task-board-category-container">
    <span class="task-board-category">
        <?= Helper\a(
            Helper\in_list($task['category_id'], $categories),
            'board',
            'changeCategory',
            array('task_id' => $task['id']),
            false,
            'category-popover',
            t('Change category')
        ) ?>
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
