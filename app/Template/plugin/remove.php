<div class="page-header">
    <h2><?= t('Remove plugin') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info"><?= t('Do you really want to remove this plugin: "%s"?', $plugin->getPluginName()) ?></p>

    <?= $this->modal->confirmButtons(
        'PluginController',
        'uninstall',
        array('pluginId' => $plugin_id)
    ) ?>
</div>
