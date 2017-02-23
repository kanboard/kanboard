<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><strong><?= t('Sort') ?> <i class="fa fa-caret-down"></i></strong></a>
    <ul>
        <li>
            <?= $paginator->order(t('Project ID'), \Kanboard\Model\ProjectModel::TABLE.'.id') ?>
        </li>
        <li>
            <?= $paginator->order(t('Project name'), \Kanboard\Model\ProjectModel::TABLE.'.name') ?>
        </li>
        <li>
            <?= $paginator->order(t('Status'), \Kanboard\Model\ProjectModel::TABLE.'.is_active') ?>
        </li>
        <li>
            <?= $paginator->order(t('Start date'), \Kanboard\Model\ProjectModel::TABLE.'.start_date') ?>
        </li>
        <li>
            <?= $paginator->order(t('End date'), \Kanboard\Model\ProjectModel::TABLE.'.end_date') ?>
        </li>
        <li>
            <?= $paginator->order(t('Public'), \Kanboard\Model\ProjectModel::TABLE.'.is_public') ?>
        </li>
        <li>
            <?= $paginator->order(t('Private'), \Kanboard\Model\ProjectModel::TABLE.'.is_private') ?>
        </li>
    </ul>
</div>
