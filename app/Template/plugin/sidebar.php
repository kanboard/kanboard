<div class="sidebar">
    <ul>
        <li <?= $this->app->checkMenuSelection('PluginController', 'show') ?>>
            <?= $this->url->link(t('Installed Plugins'), 'PluginController', 'show') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('PluginController', 'directory') ?>>
            <?= $this->url->link(t('Plugin Directory'), 'PluginController', 'directory') ?>
        </li>
    </ul>
</div>
