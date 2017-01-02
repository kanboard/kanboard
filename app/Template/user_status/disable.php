<div class="page-header">
    <h2><?= t('Disable user') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info"><?= t('Do you really want to disable this user: "%s"?', $user['name'] ?: $user['username']) ?></p>

    <?= $this->modal->confirmButtons(
        'UserStatusController',
        'disable',
        array('user_id' => $user['id'])
    ) ?>
</div>
