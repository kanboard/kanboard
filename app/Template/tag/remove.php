<div class="page-header">
    <h2><?= t('Remove a tag') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this tag: "%s"?', $tag['name']) ?>
    </p>

    <div class="form-actions">
        <?= $this->url->link(t('Yes'), 'TagController', 'remove', array('tag_id' => $tag['id']), true, 'btn btn-red popover-link') ?>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'TagController', 'index', array(), false, 'close-popover') ?>
    </div>
</div>
