<div class="page-header">
    <h2><?= t('Remove a column') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this column: "%s"?', $column['title']) ?>
        <?= t('This action will REMOVE ALL TASKS associated to this column!') ?>
    </p>

    <div class="form-actions">
        <a href="?controller=board&amp;action=remove&amp;column_id=<?= $column['id'].Helper\param_csrf() ?>" class="btn btn-red"><?= t('Yes') ?></a>
        <?= t('or') ?> <a href="?controller=board&amp;action=edit&amp;project_id=<?= $column['project_id'] ?>"><?= t('cancel') ?></a>
    </div>
</div>