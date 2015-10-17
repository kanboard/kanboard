<div class="page-header">
    <h2><?= t('Plugins') ?></h2>
</div>

<?php if (empty($plugins)): ?>
    <p class="alert"><?= t('There is no plugin loaded.') ?></p>
<?php else: ?>
    <table class="table-stripped">
        <tr>
            <th class="column-20"><?= t('Name') ?></th>
            <th class="column-20"><?= t('Author') ?></th>
            <th class="column-10"><?= t('Version') ?></th>
            <th><?= t('Description') ?></th>
        </tr>

    <?php foreach ($plugins as $plugin): ?>
    <tr>
        <td>
            <?php if ($plugin->getPluginHomepage()): ?>
                <a href="<?= $plugin->getPluginHomepage() ?>" target="_blank" rel="noreferrer"><?= $this->e($plugin->getPluginName()) ?></a>
            <?php else: ?>
                <?= $this->e($plugin->getPluginName()) ?>
            <?php endif ?>
        </td>
        <td><?= $this->e($plugin->getPluginAuthor()) ?></td>
        <td><?= $this->e($plugin->getPluginVersion()) ?></td>
        <td><?= $this->e($plugin->getPluginDescription()) ?></td>
    </tr>
    <?php endforeach ?>
<?php endif ?>