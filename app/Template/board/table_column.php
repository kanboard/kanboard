<!-- column titles -->
<tr class="board-swimlane-columns-<?= $swimlane['id'] ?>">
    <?php foreach ($swimlane['columns'] as $column): ?>
    <th class="board-column-header board-column-header-<?= $column['id'] ?>" data-column-id="<?= $column['id'] ?>">

        <!-- column in collapsed mode -->
        <div class="board-column-collapsed">
            <span title="<?= t('Task count') ?>" class="board-column-header-task-count" title="<?= t('Show this column') ?>">
                <span id="task-number-column-<?= $column['id'] ?>"><?= $column['nb_tasks'] ?></span>
            </span>
        </div>

        <!-- column in expanded mode -->
        <div class="board-column-expanded">
            <?php if (! $not_editable): ?>
                <div class="board-add-icon">
                    <?= $this->url->link('+', 'taskcreation', 'create', array('project_id' => $column['project_id'], 'column_id' => $column['id'], 'swimlane_id' => $swimlane['id']), false, 'popover', t('Add a new task')) ?>
                </div>
            <?php endif ?>

            <?php if ($swimlane['nb_swimlanes'] > 1 && ! empty($column['nb_column_tasks'])): ?>
                <span title="<?= t('Total number of tasks in this column across all swimlanes') ?>" class="board-column-header-task-count">
                    (<span><?= $column['nb_column_tasks'] ?></span>)
                </span>
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
                    <?= $column['score'] ?>
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
