<?php if ($task['category_id']): ?>
<div class="task-board-category-container">
    <span class="task-board-category">
        <?= $this->a(
            $this->inList($task['category_id'], $categories),
            'board',
            'changeCategory',
            array('task_id' => $task['id'], 'project_id' => $task['project_id']),
            false,
            'task-board-popover',
            t('Change category')
        ) ?>
    </span>
</div>
<?php endif ?>

<div class="task-board-icons">
    <?php if (! empty($task['date_due'])): ?>
        <span class="task-board-date <?= time() > $task['date_due'] ? 'task-board-date-overdue' : '' ?>">
            <i class="fa fa-calendar"></i>&nbsp;<?= dt('%b %e', $task['date_due']) ?>
        </span>
    <?php endif ?>

    <?php if (! empty($task['nb_links'])): ?>
        <span title="<?= t('Links') ?>" class="task-board-tooltip" data-href="<?= $this->u('board', 'tasklinks', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><?= $task['nb_links'] ?> <i class="fa fa-code-fork"></i></span>
    <?php endif ?>

    <?php if (! empty($task['nb_subtasks'])): ?>
        <span title="<?= t('Sub-Tasks') ?>" class="task-board-tooltip" data-href="<?= $this->u('board', 'subtasks', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><?= round($task['nb_completed_subtasks']/$task['nb_subtasks']*100, 0).'%' ?> <i class="fa fa-bars"></i></span>
    <?php endif ?>

    <?php if (! empty($task['nb_files'])): ?>
        <span title="<?= t('Attachments') ?>" class="task-board-tooltip" data-href="<?= $this->u('board', 'attachments', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><?= $task['nb_files'] ?> <i class="fa fa-paperclip"></i></span>
    <?php endif ?>

    <?php if (! empty($task['nb_comments'])): ?>
        <span title="<?= p($task['nb_comments'], t('%d comment', $task['nb_comments']), t('%d comments', $task['nb_comments'])) ?>" class="task-board-tooltip" data-href="<?= $this->u('board', 'comments', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><?= $task['nb_comments'] ?> <i class="fa fa-comment-o"></i></span>
    <?php endif ?>

    <?php if (! empty($task['description'])): ?>
        <span title="<?= t('Description') ?>" class="task-board-tooltip" data-href="<?= $this->u('board', 'description', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>">
            <i class="fa fa-file-text-o"></i>
        </span>
    <?php endif ?>
</div>