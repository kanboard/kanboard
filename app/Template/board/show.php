<table id="board"
       data-project-id="<?= $project['id'] ?>"
       data-check-interval="<?= $board_private_refresh_interval ?>"
       data-save-url="<?= Helper\u('board', 'save', array('project_id' => $project['id'])) ?>"
       data-check-url="<?= Helper\u('board', 'check', array('project_id' => $project['id'], 'timestamp' => time())) ?>"
>
<tr>
    <?php $column_with = round(100 / count($board), 2); ?>
    <?php foreach ($board as $column): ?>
    <th width="<?= $column_with ?>%">
        <div class="board-add-icon">
            <?= Helper\a('+', 'task', 'create', array('project_id' => $column['project_id'], 'column_id' => $column['id']), false, 'task-creation-popover', t('Add a new task')) ?>
        </div>
        <?= Helper\escape($column['title']) ?>
        <?php if ($column['task_limit']): ?>
            <span title="<?= t('Task limit') ?>" class="task-limit">
                (<span id="task-number-column-<?= $column['id'] ?>"><?= count($column['tasks']) ?></span>/<?= Helper\escape($column['task_limit']) ?>)
            </span>
        <?php else: ?>
            <span title="<?= t('Task count') ?>" class="task-count">
                (<span id="task-number-column-<?= $column['id'] ?>"><?= count($column['tasks']) ?></span>)
            </span>
        <?php endif ?>
    </th>
    <?php endforeach ?>
</tr>
<tr>
    <?php foreach ($board as $column): ?>
    <td
        id="column-<?= $column['id'] ?>"
        class="column <?= $column['task_limit'] && count($column['tasks']) > $column['task_limit'] ? 'task-limit-warning' : '' ?>"
        data-column-id="<?= $column['id'] ?>"
        data-task-limit="<?= $column['task_limit'] ?>"
        >
        <?php foreach ($column['tasks'] as $task): ?>
        <div class="task-board draggable-item task-<?= $task['color_id'] ?> <?= $task['date_modification'] > time() - $board_highlight_period ? 'task-board-recent' : '' ?>"
             data-task-id="<?= $task['id'] ?>"
             data-owner-id="<?= $task['owner_id'] ?>"
             data-category-id="<?= $task['category_id'] ?>"
             data-due-date="<?= $task['date_due'] ?>"
             data-task-url="<?= Helper\u('task', 'show', array('task_id' => $task['id'])) ?>"
             title="<?= t('View this task') ?>">

            <?= Helper\template('board/task', array('task' => $task, 'categories' => $categories)) ?>

        </div>
        <?php endforeach ?>
    </td>
    <?php endforeach ?>
</tr>
</table>
