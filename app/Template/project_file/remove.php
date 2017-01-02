<div class="page-header">
    <h2><?= t('Remove a file') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this file: "%s"?', $this->text->e($file['name'])) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'ProjectFileController',
        'remove',
        array('project_id' => $project['id'], 'file_id' => $file['id'])
    ) ?>
</div>
