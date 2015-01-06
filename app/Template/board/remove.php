<div class="page-header">
    <h2><?= t('Remove a column') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this column: "%s"?', $column['title']) ?>
        <?= t('This action will REMOVE ALL TASKS associated to this column!') ?>
    </p>

    <div class="form-actions">
        <?= $this->a(t('Yes'), 'board', 'remove', array('project_id' => $project['id'], 'column_id' => $column['id'], 'remove' => 'yes'), true, 'btn btn-red') ?>
        <?= t('or') ?> <?= $this->a(t('cancel'), 'board', 'edit', array('project_id' => $project['id'])) ?>
    </div>
</div>