<table id="board" data-project-id="<?= $current_project_id ?>" data-time="<?= time() ?>" data-check-interval="<?= BOARD_CHECK_INTERVAL ?>">
<tr>
    <?php $column_with = round(100 / count($board), 2); ?>
    <?php foreach ($board as $column): ?>
    <th width="<?= $column_with ?>%">
        <a href="?controller=task&amp;action=create&amp;project_id=<?= $column['project_id'] ?>&amp;column_id=<?= $column['id'] ?>" title="<?= t('Add a new task') ?>">+</a>
        <?= Helper\escape($column['title']) ?>
        <?php if ($column['task_limit']): ?>
            <span title="<?= t('Task limit') ?>" class="task-limit">
                (
                 <span id="task-number-column-<?= $column['id'] ?>"><?= count($column['tasks']) ?></span>
                 /
                 <?= Helper\escape($column['task_limit']) ?>
                )
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
        <div class="task draggable-item task-<?= $task['color_id'] ?>"
             data-task-id="<?= $task['id'] ?>"
             data-owner-id="<?= $task['owner_id'] ?>"
             data-due-date="<?= $task['date_due'] ?>"
             title="<?= t('View this task') ?>">

            <a href="?controller=task&amp;action=edit&amp;task_id=<?= $task['id'] ?>" title="<?= t('Edit this task') ?>">#<?= $task['id'] ?></a> -

            <span class="task-user">
            <?php if (! empty($task['owner_id'])): ?>
                <a href="?controller=board&amp;action=assign&amp;task_id=<?= $task['id'] ?>" title="<?= t('Change assignee') ?>"><?= t('Assigned to %s', $task['username']) ?></a>
            <?php else: ?>
                <a href="?controller=board&amp;action=assign&amp;task_id=<?= $task['id'] ?>" title="<?= t('Change assignee') ?>" class="task-nobody"><?= t('Nobody assigned') ?></a>
            <?php endif ?>
            </span>

            <?php if ($task['score']): ?>
                <span class="task-score"><?= Helper\escape($task['score']) ?></span>
            <?php endif ?>

            <div class="task-title">
                <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>" title="<?= t('View this task') ?>"><?= Helper\escape($task['title']) ?></a>
            </div>

            <div class="task-footer">
                <?php if (! empty($task['date_due'])): ?>
                <div class="task-date">
                    <?= dt('%B %e, %G', $task['date_due']) ?>
                </div>
                <?php endif ?>

                <div class="task-icons">
                    <?php if (! empty($task['nb_comments'])): ?>
                        <?= $task['nb_comments'] ?> <i class="fa fa-comment-o" title="<?= p($task['nb_comments'], t('%d comment', $task['nb_comments']), t('%d comments', $task['nb_comments'])) ?>"></i>
                    <?php endif ?>

                    <?php if (! empty($task['description'])): ?>
                        <i class="fa fa-file-text-o" title="<?= t('Description') ?>"></i>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <?php endforeach ?>
    </td>
    <?php endforeach ?>
</tr>
</table>
