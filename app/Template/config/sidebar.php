<div class="sidebar">
    <h2><?= t('Actions') ?></h2>
    <ul>
        <li>
            <?= Helper\a(t('About'), 'config', 'index') ?>
        </li>
        <li>
            <?= Helper\a(t('Application settings'), 'config', 'application') ?>
        </li>
        <li>
            <?= Helper\a(t('Board settings'), 'config', 'board') ?>
        </li>
        <li>
            <?= Helper\a(t('Webhooks'), 'config', 'webhook') ?>
        </li>
        <li>
            <?= Helper\a(t('API'), 'config', 'api') ?>
        </li>
    </ul>
</div>