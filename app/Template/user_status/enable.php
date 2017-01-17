<div class="page-header">
    <h2><?= t('Enable user') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info"><?= t('Do you really want to enable this user: "%s"?', $user['name'] ?: $user['username']) ?></p>

    <?= $this->modal->confirmButtons(
        'UserStatusController',
        'enable',
        array('user_id' => $user['id'])
    ) ?>
</div>
