<div class="page-header">
    <h2><?= t('Remove user') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info"><?= t('Do you really want to remove this user: "%s"?', $user['name'] ?: $user['username']) ?></p>

    <div class="form-actions">
        <a href="?controller=user&amp;action=remove&amp;confirmation=yes&amp;user_id=<?= $user['id'].Helper\param_csrf() ?>" class="btn btn-red"><?= t('Yes') ?></a>
        <?= t('or') ?> <a href="?controller=user&amp;action=show&amp;user_id=<?= $user['id'] ?>"><?= t('cancel') ?></a>
    </div>
</div>