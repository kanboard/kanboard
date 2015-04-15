<div class="page-header">
    <h2><?= t('Remove budget line') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info"><?= t('Do you really want to remove this budget line?') ?></p>

    <div class="form-actions">
        <?= $this->a(t('Yes'), 'budget', 'remove', array('project_id' => $project['id'], 'budget_id' => $budget_id), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?= $this->a(t('cancel'), 'budget', 'create', array('project_id' => $project['id'])) ?>
    </div>
</div>