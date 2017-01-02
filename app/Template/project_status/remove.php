<div class="page-header">
    <h2><?= t('Remove project') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this project: "%s"?', $project['name']) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'ProjectStatusController',
        'remove',
        array('project_id' => $project['id'])
    ) ?>
</div>
