<!-- column titles -->

<?= $this->hook->render('template:board:table:column:before-header-row', array('swimlane' => $swimlane)) ?>

<tr class="board-swimlane-columns-<?= $swimlane['id'] ?>">
    <?php foreach ($swimlane['columns'] as $column): ?>
    <th class="board-column-header board-column-header-<?= $column['id'] ?>" data-column-id="<?= $column['id'] ?>">

        <!-- column in collapsed mode -->
        <div class="board-column-collapsed">
            <small class="board-column-header-task-count" title="<?= t('Task count') ?>">
                <span id="task-number-column-<?= $column['id'] ?>"><span class="ui-helper-hidden-accessible"><?= t('Task count') ?> </span><?= $column['nb_tasks'] ?></span>
            </small>
        </div>

        <!-- column in expanded mode -->
        <div class="board-column-expanded-header">
            <?php if (! $not_editable && $this->projectRole->canCreateTaskInColumn($column['project_id'], $column['id'])): ?>
                <?= $this->task->getNewBoardTaskButton($swimlane, $column) ?>
            <?php endif ?>

            <?php if ($swimlane['nb_swimlanes'] > 1 && $column['nb_tasks'] > 0): ?>
            <span title="<?= t('Task count') ?>">
                (<span id="task-number-column-<?= $column['id'] ?>"><span class="ui-helper-hidden-accessible"><?= t('Task count') ?> </span><?= $column['nb_tasks'] ?></span>)
            </span>
            <?php endif ?>

            <span class="board-column-title">
                <?php if ($not_editable): ?>
                    <?= $this->text->e($column['title']) ?>
                <?php else: ?>
                    <span class="dropdown">
                        <a href="#" class="dropdown-menu"><?= $this->text->e($column['title']) ?> <i class="fa fa-caret-down"></i></a>
                        <ul>
                            <li>
                                <i class="fa fa-minus-square fa-fw"></i>
                                <a href="#" class="board-toggle-column-view" data-column-id="<?= $column['id'] ?>"><?= t('Hide this column') ?></a>
                            </li>
                            <?php if ($this->projectRole->canCreateTaskInColumn($column['project_id'], $column['id'])): ?>
                                <li>
                                    <?= $this->modal->medium('align-justify', t('Create tasks in bulk'), 'TaskBulkController', 'show', array('project_id' => $column['project_id'], 'column_id' => $column['id'], 'swimlane_id' => $swimlane['id'])) ?>
                                </li>
                            <?php endif ?>

                            <?php if ($column['nb_tasks'] > 0 && $this->projectRole->canChangeTaskStatusInColumn($column['project_id'], $column['id'])): ?>
                                <li>
                                    <?= $this->modal->confirm('close', t('Close all tasks of this column'), 'BoardPopoverController', 'confirmCloseColumnTasks', array('project_id' => $column['project_id'], 'column_id' => $column['id'], 'swimlane_id' => $swimlane['id'])) ?>
                                </li>
                            <?php endif ?>

                            <?php if ($column['nb_tasks'] > 0 && $this->user->hasProjectAccess('TaskModificationController', 'update', $column['project_id'])): ?>
                                <li>
                                    <?= $this->url->icon('sort-numeric-asc', t('Reorder this column by id (ASC)'), 'TaskReorderController', 'reorderColumn', ['sort' => 'id', 'direction' => 'asc', 'project_id' => $column['project_id'], 'column_id' => $column['id'], 'swimlane_id' => $swimlane['id']]) ?>
                                </li>
                                <li>
                                    <?= $this->url->icon('sort-numeric-desc', t('Reorder this column by id (DESC)'), 'TaskReorderController', 'reorderColumn', ['sort' => 'id', 'direction' => 'desc', 'project_id' => $column['project_id'], 'column_id' => $column['id'], 'swimlane_id' => $swimlane['id']]) ?>
                                </li>
                                <li>
                                    <?= $this->url->icon('sort-numeric-asc', t('Reorder this column by priority (ASC)'), 'TaskReorderController', 'reorderColumn', ['sort' => 'priority', 'direction' => 'asc', 'project_id' => $column['project_id'], 'column_id' => $column['id'], 'swimlane_id' => $swimlane['id']]) ?>
                                </li>
                                <li>
                                    <?= $this->url->icon('sort-numeric-desc', t('Reorder this column by priority (DESC)'), 'TaskReorderController', 'reorderColumn', ['sort' => 'priority', 'direction' => 'desc', 'project_id' => $column['project_id'], 'column_id' => $column['id'], 'swimlane_id' => $swimlane['id']]) ?>
                                </li>
                                <li>
                                    <?= $this->url->icon('sort-amount-asc', t('Reorder this column by assignee and priority (ASC)'), 'TaskReorderController', 'reorderColumn', ['sort' => 'assignee-priority', 'direction' => 'asc', 'project_id' => $column['project_id'], 'column_id' => $column['id'], 'swimlane_id' => $swimlane['id']]) ?>
                                </li>
                                <li>
                                    <?= $this->url->icon('sort-amount-desc', t('Reorder this column by assignee and priority (DESC)'), 'TaskReorderController', 'reorderColumn', ['sort' => 'assignee-priority', 'direction' => 'desc', 'project_id' => $column['project_id'], 'column_id' => $column['id'], 'swimlane_id' => $swimlane['id']]) ?>
                                </li>
                                <li>
                                    <?= $this->url->icon('sort-alpha-asc', t('Reorder this column by assignee (A-Z)'), 'TaskReorderController', 'reorderColumn', ['sort' => 'assignee', 'direction' => 'asc', 'project_id' => $column['project_id'], 'column_id' => $column['id'], 'swimlane_id' => $swimlane['id']]) ?>
                                </li>
                                <li>
                                    <?= $this->url->icon('sort-alpha-desc', t('Reorder this column by assignee (Z-A)'), 'TaskReorderController', 'reorderColumn', ['sort' => 'assignee', 'direction' => 'desc', 'project_id' => $column['project_id'], 'column_id' => $column['id'], 'swimlane_id' => $swimlane['id']]) ?>
                                </li>
                                <li>
                                    <?= $this->url->icon('sort-numeric-asc', t('Reorder this column by due date (ASC)'), 'TaskReorderController', 'reorderColumn', ['sort' => 'due-date', 'direction' => 'asc', 'project_id' => $column['project_id'], 'column_id' => $column['id'], 'swimlane_id' => $swimlane['id']]) ?>
                                </li>
                                <li>
                                    <?= $this->url->icon('sort-numeric-desc', t('Reorder this column by due date (DESC)'), 'TaskReorderController', 'reorderColumn', ['sort' => 'due-date', 'direction' => 'desc', 'project_id' => $column['project_id'], 'column_id' => $column['id'], 'swimlane_id' => $swimlane['id']]) ?>
                                </li>
                            <?php endif ?>

                            <?= $this->hook->render('template:board:column:dropdown', array('swimlane' => $swimlane, 'column' => $column)) ?>
                        </ul>
                    </span>
                <?php endif ?>
            </span>

            <span class="pull-right">
                <?php if ($swimlane['nb_swimlanes'] > 1 && ! empty($column['column_score'])): ?>
                    <span title="<?= t('Total score in this column across all swimlanes') ?>">
                        (<span><span class="ui-helper-hidden-accessible"><?= t('Total score in this column across all swimlanes') ?> </span><?= $column['column_score'] ?></span>)
                    </span>
                <?php endif ?>

                <?php if (! empty($column['score'])): ?>
                    <span title="<?= t('Score') ?>">
                        <span class="ui-helper-hidden-accessible"><?= t('Score') ?> </span><?= $column['score'] ?>
                    </span>
                <?php endif ?>

                <?php if (! $not_editable && ! empty($column['description'])): ?>
                    <?= $this->app->tooltipMarkdown($column['description']) ?>
                <?php endif ?>
            </span>

            <?php if (! empty($column['column_nb_tasks'])): ?>
            <span title="<?= t('Task count') ?>" class="board-column-header-task-count">
                <span><span class="ui-helper-hidden-accessible"><?= t('Task count') ?> </span><?= $column['column_nb_tasks'] ?></span>
            </span>
            <?php endif ?>
            <?php if (! empty($column['column_nb_open_tasks'])): ?>
            <span title="<?= t('Total number of tasks in this column across all swimlanes') ?>" class="board-column-header-task-count">
                <?php if ($column['task_limit'] > 0): ?>
                    (<span><span class="ui-helper-hidden-accessible"><?= t('Total number of tasks in this column across all swimlanes') ?> </span><?= $column['column_nb_open_tasks'] ?></span> / <span title="<?= t('Task limit') ?>"><span class="ui-helper-hidden-accessible"><?= t('Task limit') ?> </span><?= $this->text->e($column['task_limit']) ?></span>)
                <?php else: ?>
                    (<span><span class="ui-helper-hidden-accessible"><?= t('Total number of tasks in this column across all swimlanes') ?> </span><?= $column['column_nb_open_tasks'] ?></span>)
                <?php endif ?>
            </span>
            <?php endif ?>
            <?= $this->hook->render('template:board:column:header', array('swimlane' => $swimlane, 'column' => $column)) ?>
        </div>

    </th>
    <?php endforeach ?>
</tr>

<?= $this->hook->render('template:board:table:column:after-header-row', array('swimlane' => $swimlane)) ?>
