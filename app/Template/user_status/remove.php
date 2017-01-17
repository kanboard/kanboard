<div class="page-header">
    <h2><?= t('Remove user') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info"><?= t('Do you really want to remove this user: "%s"?', $user['name'] ?: $user['username']) ?></p>

    <?= $this->modal->confirmButtons(
        'UserStatusController',
        'remove',
        array('user_id' => $user['id'])
    ) ?>
</div>
