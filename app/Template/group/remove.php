<div class="page-header">
    <h2><?= t('Remove group') ?></h2>
</div>
<div class="confirm">
    <p class="alert alert-info"><?= t('Do you really want to remove this group: "%s"?', $group['name']) ?></p>

    <?= $this->modal->confirmButtons(
        'GroupListController',
        'remove',
        array('group_id' => $group['id'])
    ) ?>
</div>
