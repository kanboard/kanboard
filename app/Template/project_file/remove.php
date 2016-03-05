<div class="page-header">
    <h2><?= t('Remove a file') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this file: "%s"?', $this->text->e($file['name'])) ?>
    </p>

    <div class="form-actions">
        <?= $this->url->link(t('Yes'), 'ProjectFile', 'remove', array('project_id' => $project['id'], 'file_id' => $file['id']), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'ProjectOverview', 'show', array('project_id' => $project['id']), false, 'close-popover') ?>
    </div>
</div>