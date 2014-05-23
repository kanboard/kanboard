<div class="page-header">
    <h2><?= t('Attach a document') ?></h2>
</div>

<form action="?controller=task&amp;action=upload&amp;task_id=<?= $task['id'] ?>" method="post" enctype="multipart/form-data">
    <input type="file" name="files[]" multiple />
    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>