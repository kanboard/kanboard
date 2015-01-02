<?php if ($not_editable): ?>

<div class="task-board task-<?= $task['color_id'] ?> <?= $task['date_modification'] > time() - $board_highlight_period ? 'task-board-recent' : '' ?>">

    <?= $this->a('#'.$task['id'], 'task', 'readonly', array('task_id' => $task['id'], 'token' => $project['token'])) ?>

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
        <span class="task-score"><?= $this->e($task['score']) ?></span>
    <?php endif ?>

    <div class="task-board-title">
        <?= $this->a($this->e($task['title']), 'task', 'readonly', array('task_id' => $task['id'], 'token' => $project['token'])) ?>
    </div>

<?php else: ?>

<div class="task-board draggable-item task-<?= $task['color_id'] ?> <?= $task['date_modification'] > time() - $board_highlight_period ? 'task-board-recent' : '' ?>"
     data-task-id="<?= $task['id'] ?>"
     data-owner-id="<?= $task['owner_id'] ?>"
     data-category-id="<?= $task['category_id'] ?>"
     data-due-date="<?= $task['date_due'] ?>"
     data-task-url="<?= $this->u('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"
     title="<?= t('View this task') ?>">

    <?= $this->a('#'.$task['id'], 'task', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'task-edit-popover', t('Edit this task')) ?>

    <?php if ($task['reference']): ?>
    <span class="task-board-reference" title="<?= t('Reference') ?>">
        (<?= $task['reference'] ?>)
    </span>
    <?php endif ?>

    <span class="task-board-user <?= $this->userSession->isCurrentUser($task['owner_id']) ? 'task-board-current-user' : '' ?>">
        <?= $this->a(
            (! empty($task['owner_id']) ? t('Assigned to %s', $task['assignee_name'] ?: $task['assignee_username']) : t('Nobody assigned')),
            'board',
            'changeAssignee',
            array('task_id' => $task['id'], 'project_id' => $task['project_id']),
            false,
            'assignee-popover',
            t('Change assignee')
        ) ?>
    </span>

    <?php if ($task['score']): ?>
        <span class="task-score"><?= $this->e($task['score']) ?></span>
    <?php endif ?>

    <div class="task-board-title">
        <?= $this->a($this->e($task['title']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, '', t('View this task')) ?>
    </div>

<?php endif ?>


<?php if ($task['category_id']): ?>
<div class="task-board-category-container">
    <span class="task-board-category">
        <?= $this->a(
            $this->inList($task['category_id'], $categories),
            'board',
            'changeCategory',
            array('task_id' => $task['id'], 'project_id' => $task['project_id']),
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
        <i class="fa fa-calendar"></i>&nbsp;<?= dt('%b %e, %Y', $task['date_due']) ?>
    </div>
    <?php endif ?>

    <div class="task-board-icons">

        <?php if (! empty($task['nb_subtasks'])): ?>
            <span title="<?= t('Sub-Tasks') ?>" class="task-board-tooltip" data-href="<?= $this->u('board', 'subtasks', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><?= $task['nb_completed_subtasks'].'/'.$task['nb_subtasks'] ?> <i class="fa fa-bars"></i></span>
        <?php endif ?>

        <?php if (! empty($task['nb_files'])): ?>
            <span title="<?= t('Attachments') ?>" class="task-board-tooltip" data-href="<?= $this->u('board', 'attachments', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><?= $task['nb_files'] ?> <i class="fa fa-paperclip"></i></span>
        <?php endif ?>

        <?php if (! empty($task['nb_comments'])): ?>
            <span title="<?= p($task['nb_comments'], t('%d comment', $task['nb_comments']), t('%d comments', $task['nb_comments'])) ?>" class="task-board-tooltip" data-href="<?= $this->u('board', 'comments', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><?= $task['nb_comments'] ?> <i class="fa fa-comment-o"></i></span>
        <?php endif ?>

        <?php if (! empty($task['description'])): ?>
            <span title="<?= t('Description') ?>" class="task-board-tooltip" data-href="<?= $this->u('board', 'description', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>">
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

</div>