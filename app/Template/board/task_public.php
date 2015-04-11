<div class="task-board color-<?= $task['color_id'] ?> <?= $task['date_modification'] > time() - $board_highlight_period ? 'task-board-recent' : '' ?>">

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

    <div class="task-board-title">
        <?= $this->a($this->e($task['title']), 'task', 'readonly', array('task_id' => $task['id'], 'token' => $project['token'])) ?>
    </div>

    <?= $this->render('board/task_footer', array(
        'task' => $task,
        'categories_listing' => $categories_listing,
        'categories_description' => $categories_description,
        'not_editable' => $not_editable,
    )) ?>
</div>