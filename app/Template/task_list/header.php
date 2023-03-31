<div class="table-list-header">
    <div class="table-list-header-count">
        <?php if ($paginator->getTotal() > 1): ?>
            <?= t('%d tasks', $paginator->getTotal()) ?>
        <?php else: ?>
            <?= t('%d task', $paginator->getTotal()) ?>
        <?php endif ?>
    </div>
    <?php if (isset($show_items_selection)): ?>
        <?php if ($this->user->hasProjectAccess('TaskModificationController', 'save', $project['id'])): ?>
            <div class="list-item-links">
                <a href="#" data-list-item-selection="all"><?= t('Select All') ?></a> / <a href="#" data-list-item-selection="none"><?= t('Unselect All') ?></a>
            </div>
            <div class="list-item-actions list-item-action-hidden">
                -&nbsp;
                <div class="dropdown">
                    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><strong><?= t('Apply action') ?> <i class="fa fa-caret-down"></i></strong></a>
                    <ul>
                        <li>
                            <a href="<?= $this->url->href('TaskBulkMoveColumnController', 'show', ['project_id' => $project['id']]) ?>" data-list-item-action="modal"><?= t('Move selected tasks to another column or swimlane') ?></a>
                        </li>
                        <li>
                            <a href="<?= $this->url->href('TaskBulkChangePropertyController', 'show', ['project_id' => $project['id']]) ?>" data-list-item-action="modal"><?= t('Edit tasks in bulk') ?></a>
                        </li>
                    </ul>
                </div>
            </div>
        <?php endif ?>
    <?php endif ?>
    <div class="table-list-header-menu">
        <?php if (isset($project)): ?>
            <?php if ($this->user->hasSubtaskListActivated()): ?>
                <?= $this->url->icon('tasks', t('Hide subtasks'), 'TaskListController', 'show', array('project_id' => $project['id'], 'hide_subtasks' => 1, 'csrf_token' => $this->app->getToken()->getReusableCSRFToken())) ?>
            <?php else: ?>
                <?= $this->url->icon('tasks', t('Show subtasks'), 'TaskListController', 'show', array('project_id' => $project['id'], 'show_subtasks' => 1, 'csrf_token' => $this->app->getToken()->getReusableCSRFToken())) ?>
            <?php endif ?>
        <?php endif ?>

        <?= $this->render('task_list/sort_menu', array('paginator' => $paginator)) ?>
    </div>
</div>