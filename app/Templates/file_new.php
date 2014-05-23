<div class="page-header">
    <h2><?= t('Attach a document') ?></h2>
</div>

<form action="?controller=file&amp;action=save&amp;task_id=<?= $task['id'] ?>" method="post" enctype="multipart/form-data">
    <input type="file" name="files[]" multiple />
    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>"><?= t('cancel') ?></a>
    </div>
</form>