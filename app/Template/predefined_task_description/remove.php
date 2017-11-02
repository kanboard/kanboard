<div class="page-header">
    <h2><?= t('Predefined Task Description') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this template? "%s"', $template['title']) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'PredefinedTaskDescriptionController',
        'remove',
        array('project_id' => $project['id'], 'id' => $template['id'])
    ) ?>
</div>
