<div class="table-list-header">
    <div class="table-list-header-count">
        <?php if ($paginator->getTotal() > 1): ?>
            <?= t('%d tasks', $paginator->getTotal()) ?>
        <?php else: ?>
            <?= t('%d task', $paginator->getTotal()) ?>
        <?php endif ?>
    </div>
    <div class="table-list-header-menu">
        <?php if (isset($project)): ?>
            <?php if ($this->user->hasSubtaskListActivated()): ?>
                <?= $this->url->icon('tasks', t('Hide subtasks'), 'TaskListController', 'show', array('project_id' => $project['id'], 'hide_subtasks' => 1)) ?>
            <?php else: ?>
                <?= $this->url->icon('tasks', t('Show subtasks'), 'TaskListController', 'show', array('project_id' => $project['id'], 'show_subtasks' => 1)) ?>
            <?php endif ?>
        <?php endif ?>

        <?= $this->render('task_list/sort_menu', array('paginator' => $paginator)) ?>
    </div>
</div>