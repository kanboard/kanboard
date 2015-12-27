<div class="sidebar">
    <h2><?= t('Actions') ?></h2>
    <ul>
        <li <?= $this->app->checkMenuSelection('config', 'index') ?>>
            <?= $this->url->link(t('About'), 'config', 'index') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('config', 'plugins') ?>>
            <?= $this->url->link(t('Plugins'), 'config', 'plugins') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('config', 'application') ?>>
            <?= $this->url->link(t('Application settings'), 'config', 'application') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('config', 'project') ?>>
            <?= $this->url->link(t('Project settings'), 'config', 'project') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('config', 'board') ?>>
            <?= $this->url->link(t('Board settings'), 'config', 'board') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('config', 'calendar') ?>>
            <?= $this->url->link(t('Calendar settings'), 'config', 'calendar') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('link') ?>>
            <?= $this->url->link(t('Link settings'), 'link', 'index') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('currency', 'index') ?>>
            <?= $this->url->link(t('Currency rates'), 'currency', 'index') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('config', 'integrations') ?>>
            <?= $this->url->link(t('Integrations'), 'config', 'integrations') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('config', 'webhook') ?>>
            <?= $this->url->link(t('Webhooks'), 'config', 'webhook') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('config', 'api') ?>>
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