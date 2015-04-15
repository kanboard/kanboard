<tr>
    <?php if (! $hide_swimlane): ?>
       <th>

           <?php if (! $not_editable && $swimlane['nb_tasks'] > 0): ?>
                <a href="#" class="board-swimlane-toggle" data-swimlane-id="<?= $swimlane['id'] ?>">
                    <i class="fa fa-minus-circle hide-icon-swimlane-<?= $swimlane['id'] ?>"></i>
                    <i class="fa fa-plus-circle show-icon-swimlane-<?= $swimlane['id'] ?>" style="display: none"></i>
                </a>
           <?php endif ?>

           <?= $this->e($swimlane['name']) ?>

           <span title="<?= t('Task count') ?>" class="task-count">
                (<span><?= $swimlane['nb_tasks'] ?></span>)
            </span>
        </th>
    <?php endif ?>

    <?php foreach ($swimlane['columns'] as $column): ?>
    <th class="board-column">
        <?php if (! $not_editable): ?>
            <div class="board-add-icon">
                <?= $this->a('+', 'task', 'create', array('project_id' => $column['project_id'], 'column_id' => $column['id'], 'swimlane_id' => $swimlane['id']), false, 'task-board-popover', t('Add a new task')) ?>
            </div>
        <?php endif ?>

        <?= $this->e($column['title']) ?>

        <?php if (! $not_editable && ! empty($column['description'])): ?>
            <span class="column-tooltip pull-right" title='<?= $this->e($this->markdown($column['description'])) ?>'>
                <i class="fa fa-info-circle"></i>
            </span>
        <?php endif ?>

        <?php if (! empty($column['score'])): ?>
            <span class="column-score pull-right" title="<?= t('Score') ?>">
                <?= $column['score'] ?>&nbsp;
            </span>
        <?php endif ?>

        <?php if ($column['task_limit']): ?>
            <span title="<?= t('Task limit') ?>" class="task-limit">
                (<span id="task-number-column-<?= $column['id'] ?>"><?= $column['nb_tasks'] ?></span>/<?= $this->e($column['task_limit']) ?>)
            </span>
        <?php else: ?>
            <span title="<?= t('Task count') ?>" class="task-count">
                (<span id="task-number-column-<?= $column['id'] ?>"><?= $column['nb_tasks'] ?></span>)
            </span>
        <?php endif ?>
    </th>
    <?php endforeach ?>
</tr>
<tr class="swimlane-row-<?= $swimlane['id'] ?>">

    <?php if (! $hide_swimlane): ?>
        <th></th>
    <?php endif ?>

    <?php foreach ($swimlane['columns'] as $column): ?>

        <?php if ($not_editable): ?>
            <td>
        <?php else: ?>
        <td
            id="column-<?= $column['id'] ?>"
            class="column <?= $column['task_limit'] && count($column['tasks']) > $column['task_limit'] ? 'task-limit-warning' : '' ?>"
            data-column-id="<?= $column['id'] ?>"
            data-swimlane-id="<?= $swimlane['id'] ?>"
            data-task-limit="<?= $column['task_limit'] ?>">
        <?php endif ?>

        <?php foreach ($column['tasks'] as $task): ?>
            <?= $this->render($not_editable ? 'board/task_public' : 'board/task_private', array(
                'project' => $project,
                'task' => $task,
                'categories_listing' => $categories_listing,
                'categories_description' => $categories_description,
                'board_highlight_period' => $board_highlight_period,
                'not_editable' => $not_editable,
            )) ?>
        <?php endforeach ?>
    </td>
    <?php endforeach ?>
</tr>