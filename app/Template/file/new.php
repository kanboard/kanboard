<div class="page-header">
    <h2><?= t('Attach a document') ?></h2>
</div>

<form action="<?= $this->u('file', 'save', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" method="post" enctype="multipart/form-data">
    <?= $this->formCsrf() ?>
    <input type="file" name="files[]" multiple />
    <div class="form-help"><?= t('Maximum size: ') ?><?= is_integer($max_size) ? $this->formatBytes($max_size) : $max_size ?></div>
    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= $this->a(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
    </div>
</form>