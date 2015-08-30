<div class="sidebar">
    <h2><?= t('Budget') ?></h2>
    <ul>
        <li <?= $this->app->getRouterAction() === 'index' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Budget overview'), 'budget', 'index', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'create' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Budget lines'), 'budget', 'create', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'breakdown' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Cost breakdown'), 'budget', 'breakdown', array('project_id' => $project['id'])) ?>
        </li>
    </ul>
    <div class="sidebar-collapse"><a href="#" title="<?= t('Hide sidebar') ?>"><i class="fa fa-chevron-left"></i></a></div>
    <div class="sidebar-expand" style="display: none"><a href="#" title="<?= t('Expand sidebar') ?>"><i class="fa fa-chevron-right"></i></a></div>
</div>