<div class="page-header">
    <h2><?= t('Remove group') ?></h2>
</div>
<div class="confirm">
    <p class="alert alert-info"><?= t('Do you really want to remove this group: "%s"?', $group['name']) ?></p>

    <div class="form-actions">
        <?= $this->url->link(t('Yes'), 'GroupListController', 'remove', array('group_id' => $group['id']), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'GroupListController', 'index', array(), false, 'close-popover') ?>
    </div>
</div>
