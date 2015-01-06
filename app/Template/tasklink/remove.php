<div class="page-header">
    <h2><?= t('Remove a link') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this link with task #%s?', $link['task_inverse_id']) ?>
        <br />
        
    </p>

    <div class="form-actions">
        <?= $this->a(t('Yes'), 'tasklink', 'remove', array('task_id' => $task['id'], 'link_id' => $link['id'], 'project_id' => $task['project_id']), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?= $this->a(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
    </div>
</div>