<div class="page-header">
    <h2><?= t('Remove a file') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this file: "%s"?', Helper\escape($file['name'])) ?>
    </p>

    <div class="form-actions">
        <?= Helper\a(t('Yes'), 'file', 'remove', array('task_id' => $task['id'], 'file_id' => $file['id']), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?= Helper\a(t('cancel'), 'task', 'show', array('task_id' => $task['id'])) ?>
    </div>
</div>