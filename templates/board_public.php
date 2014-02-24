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
                </th>
                <?php endforeach ?>
            </tr>
            <tr>
                <?php foreach ($columns as $column): ?>
                <td class="column">
                    <?php foreach ($column['tasks'] as $task): ?>
                    <div class="draggable-item">
                        <div class="task task-<?= $task['color_id'] ?>">

                            #<?= $task['id'] ?> -

                            <span class="task-user">
                            <?php if (! empty($task['owner_id'])): ?>
                                <?= t('Assigned to %s', $task['username']) ?>
                            <?php else: ?>
                                <span class="task-nobody"><?= t('No body assigned') ?></span>
                            <?php endif ?>
                            </span>

                            <div class="task-title">
                                <?= Helper\escape($task['title']) ?>
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