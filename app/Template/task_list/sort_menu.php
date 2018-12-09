<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><strong><?= t('Sort') ?> <i class="fa fa-caret-down"></i></strong></a>
    <ul>
        <li>
            <?= $paginator->order(t('Task ID'), \Kanboard\Model\TaskModel::TABLE.'.id') ?>
        </li>
        <li>
            <?= $paginator->order(t('Swimlane'), 'swimlane_name') ?>
        </li>
        <li>
            <?= $paginator->order(t('Column'), 'column_name') ?>
        </li>
        <li>
            <?= $paginator->order(t('Category'), 'category_name') ?>
        </li>
        <li>
            <?= $paginator->order(t('Priority'), \Kanboard\Model\TaskModel::TABLE.'.priority') ?>
        </li>
        <li>
            <?= $paginator->order(t('Position'), \Kanboard\Model\TaskModel::TABLE.'.position') ?>
        </li>
        <li>
            <?= $paginator->order(t('Title'), \Kanboard\Model\TaskModel::TABLE.'.title') ?>
        </li>
        <li>
            <?= $paginator->order(t('Assignee'), 'assignee_name') ?>
        </li>
        <li>
            <?= $paginator->order(t('Due date'), \Kanboard\Model\TaskModel::TABLE.'.date_due') ?>
        </li>
        <li>
            <?= $paginator->order(t('Start date'), \Kanboard\Model\TaskModel::TABLE.'.date_started') ?>
        </li>
        <li>
            <?= $paginator->order(t('Status'), \Kanboard\Model\TaskModel::TABLE.'.is_active') ?>
        </li>
        <li>
            <?= $paginator->order(t('Reference'), \Kanboard\Model\TaskModel::TABLE.'.reference') ?>
        </li>
    </ul>
</div>
