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
                    <div class="task-board task-<?= $task['color_id'] ?>">

                        <?= Helper\template('board/task', array(
                            'task' => $task,
                            'categories' => $categories,
                            'not_editable' => true,
                            'project' => $project
                        )) ?>

                    </div>
                    <?php endforeach ?>
                </td>
                <?php endforeach ?>
            </tr>
        </table>
    <?php endif ?>

</section>