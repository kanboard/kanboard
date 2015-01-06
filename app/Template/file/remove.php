<div class="page-header">
    <h2><?= t('Remove a file') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this file: "%s"?', $this->e($file['name'])) ?>
    </p>

    <div class="form-actions">
        <?= $this->a(t('Yes'), 'file', 'remove', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'file_id' => $file['id']), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?= $this->a(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
    </div>
</div>