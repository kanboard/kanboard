<tr id="swimlane-<?= $swimlane['id'] ?>">
    <!-- swimlane toggle -->
    <?php if (! $hide_swimlane): ?>
       <th class="board-swimlane-header">
           <?php if (! $not_editable): ?>
                <a href="#" class="board-swimlane-toggle" data-swimlane-id="<?= $swimlane['id'] ?>">
                    <i class="fa fa-minus-circle hide-icon-swimlane-<?= $swimlane['id'] ?>"></i>
                    <i class="fa fa-plus-circle show-icon-swimlane-<?= $swimlane['id'] ?>" style="display: none"></i>
                </a>
                <span class="board-swimlane-toggle-title show-icon-swimlane-<?= $swimlane['id'] ?>"><?= $this->e($swimlane['name']) ?></span>
           <?php endif ?>
        </th>
    <?php endif ?>

    <!-- column header title -->
    <?php foreach ($swimlane['columns'] as $column): ?>
    <th class="board-column-header board-column-header-<?= $column['id'] ?>" data-column-id="<?= $column['id'] ?>">
        <div class="board-column-collapsed">
            <span title="<?= t('Task count') ?>" class="board-column-header-task-count" title="<?= t('Show this column') ?>">
                <span id="task-number-column-<?= $column['id'] ?>"><?= $column['nb_tasks'] ?></span>
            </span>
        </div>
        <div class="board-column-expanded">
            <?php if (! $not_editable): ?>
                <div class="board-add-icon">
                    <?= $this->url->link('+', 'taskcreation', 'create', array('project_id' => $column['project_id'], 'column_id' => $column['id'], 'swimlane_id' => $swimlane['id']), false, 'popover', t('Add a new task')) ?>
                </div>
            <?php endif ?>

            <span class="board-column-title" data-column-id="<?= $column['id'] ?>" title="<?= t('Hide this column') ?>">
                <?= $this->e($column['title']) ?>
            </span>

            <?php if (! $not_editable && ! empty($column['description'])): ?>
                <span class="tooltip pull-right" title='<?= $this->e($this->text->markdown($column['description'])) ?>'>
                    <i class="fa fa-info-circle"></i>
                </span>
            <?php endif ?>

            <?php if (! empty($column['score'])): ?>
                <span class="pull-right" title="<?= t('Score') ?>">
                    <?= $column['score'] ?>&nbsp;
                </span>
            <?php endif ?>

            <?php if ($column['task_limit']): ?>
                <span title="<?= t('Task limit') ?>">
                    (<span id="task-number-column-<?= $column['id'] ?>"><?= $column['nb_tasks'] ?></span>/<?= $this->e($column['task_limit']) ?>)
                </span>
            <?php else: ?>
                <span title="<?= t('Task count') ?>" class="board-column-header-task-count">
                    (<span id="task-number-column-<?= $column['id'] ?>"><?= $column['nb_tasks'] ?></span>)
                </span>
            <?php endif ?>
        </div>
    </th>
    <?php endforeach ?>
</tr>
<tr class="board-swimlane swimlane-row-<?= $swimlane['id'] ?>">

    <!-- swimlane title -->
    <?php if (! $hide_swimlane): ?>
        <th class="board-swimlane-title">
            <?= $this->e($swimlane['name']) ?>

            <div title="<?= t('Task count') ?>" class="board-column-header-task-count">
                (<span><?= $swimlane['nb_tasks'] ?></span>)
            </div>
        </th>
    <?php endif ?>

    <!-- task list -->
    <?php foreach ($swimlane['columns'] as $column): ?>
        <td class="board-column-<?= $column['id'] ?> <?= $column['task_limit'] && $column['nb_tasks'] > $column['task_limit'] ? 'board-task-list-limit' : '' ?>">
            <div class="board-task-list board-column-expanded" data-column-id="<?= $column['id'] ?>" data-swimlane-id="<?= $swimlane['id'] ?>" data-task-limit="<?= $column['task_limit'] ?>">
                <?php foreach ($column['tasks'] as $task): ?>
                    <?= $this->render($not_editable ? 'board/task_public' : 'board/task_private', array(
                        'project' => $project,
                        'task' => $task,
                        'board_highlight_period' => $board_highlight_period,
                        'not_editable' => $not_editable,
                    )) ?>
                <?php endforeach ?>
            </div>
            <div class="board-column-collapsed">
                <div class="board-rotation-wrapper">
                    <div class="board-column-title board-rotation" data-column-id="<?= $column['id'] ?>" title="<?= t('Show this column') ?>">
                        <?= $this->e($column['title']) ?>
                    </div>
                </div>
            </div>
        </td>
    <?php endforeach ?>
</tr>