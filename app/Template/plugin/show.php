<div class="page-header">
    <h2><?= t('Installed Plugins') ?></h2>
</div>

<?php if (empty($plugins)): ?>
    <p class="alert"><?= t('There is no plugin loaded.') ?></p>
<?php else: ?>
    <table>
        <tr>
            <th class="column-35"><?= t('Name') ?></th>
            <th class="column-30"><?= t('Author') ?></th>
            <th class="column-10"><?= t('Version') ?></th>
            <?php if ($is_configured): ?>
                <th><?= t('Action') ?></th>
            <?php endif ?>
        </tr>

    <?php foreach ($plugins as $pluginFolder => $plugin): ?>
    <tr>
        <td>
            <?php if ($plugin->getPluginHomepage()): ?>
                <a href="<?= $plugin->getPluginHomepage() ?>" target="_blank" rel="noreferrer"><?= $this->text->e($plugin->getPluginName()) ?></a>
            <?php else: ?>
                <?= $this->text->e($plugin->getPluginName()) ?>
            <?php endif ?>
        </td>
        <td><?= $this->text->e($plugin->getPluginAuthor()) ?></td>
        <td><?= $this->text->e($plugin->getPluginVersion()) ?></td>
        <?php if ($is_configured): ?>
            <td>
                <i class="fa fa-trash-o fa-fw" aria-hidden="true"></i>
                <?= $this->url->link(t('Uninstall'), 'PluginController', 'confirm', array('pluginId' => $pluginFolder), false, 'popover') ?>
            </td>
        <?php endif ?>
    </tr>
    <tr>
        <td colspan="<?= $is_configured ? 4 : 3 ?>"><?= $this->text->e($plugin->getPluginDescription()) ?></td>
    </tr>
    <?php endforeach ?>
    </table>
<?php endif ?>
