<div class="page-header">
    <h2><?= t('API') ?></h2>
</div>
<section class="listing">
    <ul>
        <li>
            <?= t('API token:') ?>
            <strong><?= $this->text->e($values['api_token']) ?></strong>
        </li>
        <li>
            <?= t('API endpoint:') ?>
            <input type="text" class="auto-select" readonly="readonly" value="<?= $this->url->base().'jsonrpc.php' ?>">
        </li>
        <li>
            <?= $this->url->link(t('Reset token'), 'ConfigController', 'token', array('type' => 'api'), true) ?>
        </li>
    </ul>
</section>
