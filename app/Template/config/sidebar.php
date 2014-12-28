<div class="sidebar">
    <h2><?= t('Actions') ?></h2>
    <ul>
        <li>
            <?= $this->a(t('About'), 'config', 'index') ?>
        </li>
        <li>
            <?= $this->a(t('Application settings'), 'config', 'application') ?>
        </li>
        <li>
            <?= $this->a(t('Board settings'), 'config', 'board') ?>
        </li>
        <li>
            <?= $this->a(t('Webhooks'), 'config', 'webhook') ?>
        </li>
        <li>
            <?= $this->a(t('API'), 'config', 'api') ?>
        </li>
    </ul>
</div>