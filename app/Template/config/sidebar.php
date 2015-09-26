<div class="sidebar">
    <h2><?= t('Actions') ?></h2>
    <ul>
        <li <?= $this->app->getRouterAction() === 'index' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('About'), 'config', 'index') ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'plugins' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Plugins'), 'config', 'plugins') ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'application' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Application settings'), 'config', 'application') ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'project' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Project settings'), 'config', 'project') ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'board' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Board settings'), 'config', 'board') ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'calendar' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Calendar settings'), 'config', 'calendar') ?>
        </li>
        <li <?= $this->app->getRouterController() === 'link' && $this->app->getRouterAction() === 'index' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Link settings'), 'link', 'index') ?>
        </li>
        <li <?= $this->app->getRouterController() === 'currency' && $this->app->getRouterAction() === 'index' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Currency rates'), 'currency', 'index') ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'integrations' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Integrations'), 'config', 'integrations') ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'webhook' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Webhooks'), 'config', 'webhook') ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'api' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('API'), 'config', 'api') ?>
        </li>
        <li>
            <?= $this->url->link(t('Documentation'), 'doc', 'show') ?>
        </li>
        <?= $this->hook->render('template:config:sidebar') ?>
    </ul>
    <div class="sidebar-collapse"><a href="#" title="<?= t('Hide sidebar') ?>"><i class="fa fa-chevron-left"></i></a></div>
    <div class="sidebar-expand" style="display: none"><a href="#" title="<?= t('Expand sidebar') ?>"><i class="fa fa-chevron-right"></i></a></div>
</div>