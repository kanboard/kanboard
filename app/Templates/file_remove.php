<div class="page-header">
    <h2><?= t('Remove a file') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this file: "%s"?', Helper\escape($file['name'])) ?>
    </p>

    <div class="form-actions">
        <a href="?controller=file&amp;action=remove&amp;task_id=<?= $task['id'] ?>&amp;file_id=<?= $file['id'].Helper\param_csrf() ?>" class="btn btn-red"><?= t('Yes') ?></a>
        <?= t('or') ?> <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>"><?= t('cancel') ?></a>
    </div>
</div>