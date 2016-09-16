<div class="page-header">
    <h2><?= t('Remove a column') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this column: "%s"?', $column['title']) ?>
        <?= t('This action will REMOVE ALL TASKS associated to this column!') ?>
    </p>

    <div class="form-actions">
        <?= $this->url->link(t('Yes'), 'ColumnController', 'remove', array('project_id' => $project['id'], 'column_id' => $column['id']), true, 'btn btn-red') ?>
        <?= t('or') ?> <?= $this->url->link(t('cancel'), 'ColumnController', 'index', array('project_id' => $project['id']), false, 'close-popover') ?>
    </div>
</div>
