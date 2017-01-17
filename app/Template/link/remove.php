<div class="page-header">
    <h2><?= t('Remove a link') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this link: "%s"?', $link['label']) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'LinkController',
        'remove',
        array('link_id' => $link['id'])
    ) ?>
</div>
