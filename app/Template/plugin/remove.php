<div class="page-header">
    <h2><?= t('Remove plugin') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info"><?= t('Do you really want to remove this plugin: "%s"?', $plugin->getPluginName()) ?></p>

    <div class="form-actions">
        <?= $this->url->link(t('Yes'), 'PluginController', 'uninstall', array('pluginId' => $plugin_id), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'PluginController', 'show', array(), false, 'close-popover') ?>
    </div>
</div>
