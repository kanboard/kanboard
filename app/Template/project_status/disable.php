<div class="page-header">
    <h2><?= t('Project activation') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to disable this project: "%s"?', $project['name']) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'ProjectStatusController',
        'disable',
        array('project_id' => $project['id'])
    ) ?>
</div>
