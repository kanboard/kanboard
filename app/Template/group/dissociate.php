<section id="main">
    <div class="page-header">
        <ul>
            <li><i class="fa fa-users fa-fw"></i><?= $this->url->link(t('View all groups'), 'group', 'index') ?></li>
            <li><i class="fa fa-user fa-fw"></i><?= $this->url->link(t('View group members'), 'group', 'users', array('group_id' => $group['id'])) ?></li>
        </ul>
    </div>
    <div class="confirm">
        <p class="alert alert-info"><?= t('Do you really want to remove the user "%s" from the group "%s"?', $user['name'] ?: $user['username'], $group['name']) ?></p>

        <div class="form-actions">
            <?= $this->url->link(t('Yes'), 'group', 'removeUser', array('group_id' => $group['id'], 'user_id' => $user['id']), true, 'btn btn-red') ?>
            <?= t('or') ?>
            <?= $this->url->link(t('cancel'), 'group', 'users', array('group_id' => $group['id'])) ?>
        </div>
    </div>
</section>
