<div class="page-header">
    <h2><?= t('Remove project') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this project: "%s"?', $project['name']) ?>
    </p>

    <div class="form-actions">
        <?= Helper\a(t('Yes'), 'project', 'remove', array('project_id' => $project['id'], 'remove' => 'yes'), true, 'btn btn-red') ?>
        <?= t('or') ?> <?= Helper\a(t('cancel'), 'project', 'show', array('project_id' => $project['id'])) ?>
    </div>
</div>