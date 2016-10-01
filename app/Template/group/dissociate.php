<div class="page-header">
    <h2><?= t('Remove user from group "%s"', $group['name']) ?></h2>
</div>
<div class="confirm">
    <p class="alert alert-info"><?= t('Do you really want to remove the user "%s" from the group "%s"?', $user['name'] ?: $user['username'], $group['name']) ?></p>

    <div class="form-actions">
        <?= $this->url->link(t('Yes'), 'GroupListController', 'removeUser', array('group_id' => $group['id'], 'user_id' => $user['id']), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'GroupListController', 'users', array('group_id' => $group['id']), false, 'close-popover') ?>
    </div>
</div>
