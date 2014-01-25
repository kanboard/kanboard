<section id="main">

    <div class="page-header">
        <h2><?= t('Project "%s"', $current_project_name) ?></h2>
        <ul>
            <?php foreach ($projects as $project_id => $project_name): ?>
            <?php if ($project_id != $current_project_id): ?>
            <li>
                <a href="?controller=board&amp;action=show&amp;project_id=<?= $project_id ?>"><?= Helper\escape($project_name) ?></a>
            </li>
            <?php endif ?>
            <?php endforeach ?>
        </ul>
    </div>

    <table id="board" data-project-id="<?= $current_project_id ?>">
        <tr>
            <?php $column_with = round(100 / count($columns), 2); ?>
            <?php foreach ($columns as $column): ?>
            <th width="<?= $column_with ?>%">
                <a href="?controller=task&amp;action=create&amp;project_id=<?= $column['project_id'] ?>&amp;column_id=<?= $column['id'] ?>" title="<?= t('Add a new task') ?>">+</a>
                <?= Helper\escape($column['title']) ?>
            </th>
            <?php endforeach ?>
        </tr>
        <tr>
            <?php foreach ($columns as $column): ?>
            <td id="column-<?= $column['id'] ?>" class="column" data-column-id="<?= $column['id'] ?>" dropzone="copy">
                <?php foreach ($column['tasks'] as $task): ?>
                <div class="draggable-item" draggable="true">
                    <div class="task task-<?= $task['color_id'] ?>" data-task-id="<?= $task['id'] ?>">

                        <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>" title="<?= t('View this task') ?>">#<?= $task['id'] ?></a> -

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

</section>

<script type="text/javascript" src="assets/js/board.js"></script>