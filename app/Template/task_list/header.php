<div class="task-list-header">
    <div class="task-list-header-count">
        <?php if ($paginator->getTotal() > 1): ?>
            <?= t('%d tasks', $paginator->getTotal()) ?>
        <?php else: ?>
            <?= t('%d task', $paginator->getTotal()) ?>
        <?php endif ?>
    </div>
    <div class="task-list-header-menu">
        <?= $this->render('task_list/sort_menu', array('paginator' => $paginator)) ?>
    </div>
</div>