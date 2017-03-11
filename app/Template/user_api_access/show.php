<div class="page-header">
    <h2><?= t('API User Access') ?></h2>
</div>

<p class="alert">
    <?php if (empty($user['api_access_token'])): ?>
        <?= t('No personal API access token registered.') ?>
    <?php else: ?>
        <?= t('Your personal API access token is "%s"', $user['api_access_token']) ?>
    <?php endif ?>
</p>

<?php if (! empty($user['api_access_token'])): ?>
    <?= $this->url->link(t('Remove your token'), 'UserApiAccessController', 'remove', array('user_id' => $user['id']), true, 'btn btn-red js-modal-replace') ?>
<?php endif ?>

<?= $this->url->link(t('Generate a new token'), 'UserApiAccessController', 'generate', array('user_id' => $user['id']), true, 'btn btn-blue js-modal-replace') ?>
