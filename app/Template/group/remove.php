<section id="main">
    <div class="page-header">
        <ul class="btn-group">
            <li><?= $this->url->button('users', t('View all groups'), 'group', 'index') ?></li>
            <li><?= $this->url->button('user', t('View group members'), 'group', 'users', array('group_id' => $group['id'])) ?></li>
        </ul>
    </div>
    <div class="confirm">
        <p class="alert alert-info"><?= t('Do you really want to remove this group: "%s"?', $group['name']) ?></p>

        <div class="form-actions">
            <?= $this->url->link(t('Yes'), 'group', 'remove', array('group_id' => $group['id']), true, 'btn btn-red') ?>
            <?= t('or') ?>
            <?= $this->url->link(t('cancel'), 'group', 'index') ?>
        </div>
    </div>
</section>
