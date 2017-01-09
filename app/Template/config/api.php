<div class="page-header">
    <h2><?= t('API') ?></h2>
</div>
<div class="panel">
    <ul>
        <li>
            <?= t('API token:') ?>
            <strong><?= $this->text->e($values['api_token']) ?></strong>
        </li>
        <li>
            <?= t('API endpoint:') ?>
            <strong><?= $this->url->base().'jsonrpc.php' ?></strong>
        </li>
    </ul>
</div>

<?= $this->url->link(t('Reset token'), 'ConfigController', 'token', array('type' => 'api'), true, 'btn btn-red') ?>

