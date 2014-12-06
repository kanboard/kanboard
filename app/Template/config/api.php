<div class="page-header">
    <h2><?= t('API') ?></h2>
</div>
<section class="listing">
    <ul>
        <li>
            <?= t('API token:') ?>
            <strong><?= Helper\escape($values['api_token']) ?></strong>
        </li>
        <li>
            <?= t('API endpoint:') ?>
            <input type="text" class="auto-select" readonly="readonly" value="<?= Helper\get_current_base_url().'jsonrpc.php' ?>">
        </li>
        <li>
            <?= Helper\a(t('Reset token'), 'config', 'token', array('type' => 'api'), true) ?>
        </li>
    </ul>
</section>