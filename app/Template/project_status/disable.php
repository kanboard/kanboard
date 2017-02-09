<div class="page-header">
    <h2><?= t('Close a project') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to close this project: "%s"?', $project['name']) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'ProjectStatusController',
        'disable',
        array('project_id' => $project['id'])
    ) ?>
</div>
