<tr>
    <?php if (! $hide_swimlane): ?>
       <th width="12%" >
           <?php 
           $task_count = 0;
           foreach ($swimlane['columns'] as $column): 
               $task_count += count ($column['tasks']);
           endforeach ?>
           <?php if ($task_count > 0): ?>
           <span class="board-swimlane-showhide">
               <a href="#" class="row_toggler board-swimlane-showhide" row_id="<?= $swimlane['id'] ?>">
               <i class="fa fa-minus-circle hide_<?= $swimlane['id'] ?>"></i>
               <i class="fa fa-plus-circle show_<?= $swimlane['id'] ?>" style="display:none"></i></a>
               </span>
           <?php endif ?>
           <?= $this->e($swimlane['name']) ?>
        </th>
    <?php endif ?>

    <?php foreach ($swimlane['columns'] as $column): ?>
    <th>
        <?php if (! $not_editable): ?>
            <div class="board-add-icon">
                <?= $this->a('+', 'task', 'create', array('project_id' => $column['project_id'], 'column_id' => $column['id'], 'swimlane_id' => $swimlane['id']), false, 'task-creation-popover', t('Add a new task')) ?>
            </div>
        <?php endif ?>

        <?= $this->e($column['title']) ?>

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
<tr class="cell_<?= $swimlane['id'] ?>">
    <?php if (! $hide_swimlane): ?>
        <th class="board-swimlane-title"></th>
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
            <?= $this->render('board/task', array(
                'project' => $project,
                'task' => $task,
                'categories' => $categories,
                'board_highlight_period' => $board_highlight_period,
                'not_editable' => $not_editable,
            )) ?>
        <?php endforeach ?>
    </td>
    <?php endforeach ?>
</tr>