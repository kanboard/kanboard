<div class="sidebar">
    <ul>
        <li <?= $this->app->checkMenuSelection('ConfigController', 'index') ?>>
            <?= $this->url->link(t('About'), 'ConfigController', 'index') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ConfigController', 'application') ?>>
            <?= $this->url->link(t('Application settings'), 'ConfigController', 'application') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ConfigController', 'email') ?>>
            <?= $this->url->link(t('Email settings'), 'ConfigController', 'email') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ConfigController', 'project') ?>>
            <?= $this->url->link(t('Project settings'), 'ConfigController', 'project') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ConfigController', 'board') ?>>
            <?= $this->url->link(t('Board settings'), 'ConfigController', 'board') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('TagController', 'index') ?>>
            <?= $this->url->link(t('Tags management'), 'TagController', 'index') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('LinkController') ?>>
            <?= $this->url->link(t('Link labels'), 'LinkController', 'show') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('CurrencyController') ?>>
            <?= $this->url->link(t('Currency rates'), 'CurrencyController', 'show') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ConfigController', 'integrations') ?>>
            <?= $this->url->link(t('Integrations'), 'ConfigController', 'integrations') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ConfigController', 'webhook') ?>>
            <?= $this->url->link(t('Webhooks'), 'ConfigController', 'webhook') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ConfigController', 'api') ?>>
            <?= $this->url->link(t('API'), 'ConfigController', 'api') ?>
        </li>
        <?= $this->hook->render('template:config:sidebar') ?>
    </ul>
</div>
