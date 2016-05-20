<div class="page-header">
    <h2><?= t('Plugin Directory') ?></h2>
</div>

<?php if (! $is_configured): ?>
<p class="alert alert-error">
    <?= t('Your Kanboard instance is not configured to install plugins from the user interface.') ?>
</p>
<?php endif ?>

<?php if (empty($available_plugins)): ?>
    <p class="alert"><?= t('There is no plugin available.') ?></p>
<?php else: ?>
    <?php foreach ($available_plugins as $plugin): ?>
    <table>
        <tr>
            <th colspan="3">
                <a href="<?= $plugin['homepage'] ?>" target="_blank" rel="noreferrer"><?= $this->text->e($plugin['title']) ?></a>
            </th>
        </tr>
        <tr>
            <td class="column-40">
                <?= $this->text->e($plugin['author']) ?>
            </td>
            <td class="column-30">
                <?= $this->text->e($plugin['version']) ?>
            </td>
            <td>
                <?php if ($is_configured): ?>
                    <?php if (! isset($installed_plugins[$plugin['title']])): ?>
                        <i class="fa fa-cloud-download fa-fw" aria-hidden="true"></i>
                        <?= $this->url->link(t('Install'), 'PluginController', 'install', array('archive_url' => urlencode($plugin['download'])), true) ?>
                    <?php elseif ($installed_plugins[$plugin['title']] < $plugin['version']): ?>
                        <i class="fa fa-refresh fa-fw" aria-hidden="true"></i>
                        <?= $this->url->link(t('Update'), 'PluginController', 'update', array('archive_url' => urlencode($plugin['download'])), true) ?>
                    <?php else: ?>
                        <i class="fa fa-check-circle-o" aria-hidden="true"></i>
                        <?= t('Up to date') ?>
                    <?php endif ?>
                <?php else: ?>
                    <i class="fa fa-ban fa-fw" aria-hidden="true"></i>
                    <?= t('Not available') ?>
                <?php endif ?>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <div class="markdown">
                    <?= $this->text->markdown($plugin['description']) ?>
                </div>
            </td>
        </tr>
    </table>
    <?php endforeach ?>
<?php endif ?>
