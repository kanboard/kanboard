<section id="main" class="public-board">

    <?php if (empty($columns)): ?>
        <p class="alert alert-error"><?= t('There is no column in your project!') ?></p>
    <?php else: ?>
        <table id="board">
            <tr>
                <?php $column_with = round(100 / count($columns), 2); ?>
                <?php foreach ($columns as $column): ?>
                <th width="<?= $column_with ?>%">
                    <?= Helper\escape($column['title']) ?>
                    <?php if ($column['task_limit']): ?>
                        <span title="<?= t('Task limit') ?>" class="task-limit">(<?= Helper\escape(count($column['tasks']).'/'.$column['task_limit']) ?>)</span>
                    <?php endif ?>
                </th>
                <?php endforeach ?>
            </tr>
            <tr>
                <?php foreach ($columns as $column): ?>
                <td class="column <?= $column['task_limit'] && count($column['tasks']) > $column['task_limit'] ? 'task-limit-warning' : '' ?>">
                    <?php foreach ($column['tasks'] as $task): ?>
                    <div class="draggable-item">
                        <div class="task task-<?= $task['color_id'] ?>">

                            #<?= $task['id'] ?> -

                            <span class="task-user">
                            <?php if (! empty($task['owner_id'])): ?>
                                <?= t('Assigned to %s', $task['username']) ?>
                            <?php else: ?>
                                <span class="task-nobody"><?= t('Nobody assigned') ?></span>
                            <?php endif ?>
                            </span>

                            <?php if ($task['score']): ?>
                                <span class="task-score"><?= Helper\escape($task['score']) ?></span>
                            <?php endif ?>

                            <div class="task-title">
                                <?= Helper\escape($task['title']) ?>
                            </div>

                            <div class="task-footer">
                                <?php if (! empty($task['date_due'])): ?>
                                <div class="task-date">
                                    <?= dt('%B %e, %G', $task['date_due']) ?>
                                </div>
                                <?php endif ?>

                                <?php if (! empty($task['nb_comments'])): ?>
                                <div class="task-comment-counter">
                                    <?= $task['nb_comments'] ?>
                                </div>
                                <?php endif ?>
                            </div>

                        </div>
                    </div>
                    <?php endforeach ?>
                </td>
                <?php endforeach ?>
            </tr>
        </table>
    <?php endif ?>

</section>