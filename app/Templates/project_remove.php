<div class="page-header">
    <h2><?= t('Remove project') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this project: "%s"?', $project['name']) ?>
    </p>

    <div class="form-actions">
        <a href="?controller=project&amp;action=remove&amp;project_id=<?= $project['id'].Helper\param_csrf() ?>" class="btn btn-red"><?= t('Yes') ?></a>
        <?= t('or') ?> <a href="?controller=project&amp;action=show&amp;project_id=<?= $project['id'] ?>"><?= t('cancel') ?></a>
    </div>
</div>