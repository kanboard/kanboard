<div class="page-header">
    <h2><?= t('Remove a column') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this column: "%s"?', $column['title']) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'ColumnController',
        'remove',
        array('project_id' => $project['id'], 'column_id' => $column['id'])
    ) ?>
</div>
