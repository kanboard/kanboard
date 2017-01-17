<div class="page-header">
    <h2><?= t('Disable two factor authentication') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to disable the two factor authentication for this user: "%s"?', $user['name'] ?: $user['username']) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'TwoFactorController',
        'disable',
        array('user_id' => $user['id'], 'disable' => 'yes')
    ) ?>
</div>
