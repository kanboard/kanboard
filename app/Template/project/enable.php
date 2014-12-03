<div class="page-header">
    <h2><?= t('Project activation') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to enable this project: "%s"?', $project['name']) ?>
    </p>

    <div class="form-actions">
        <?= Helper\a(t('Yes'), 'project', 'enable', array('project_id' => $project['id'], 'enable' => 'yes'), true, 'btn btn-red') ?>
        <?= t('or') ?> <?= Helper\a(t('cancel'), 'project', 'show', array('project_id' => $project['id'])) ?>
    </div>
</div>