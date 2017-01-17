<div class="page-header">
    <h2><?= t('Project activation') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to enable this project: "%s"?', $project['name']) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'ProjectStatusController',
        'enable',
        array('project_id' => $project['id'])
    ) ?>
</div>
