<div class="page-header">
    <h2><?= t('Remove a column restriction') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this column restriction?') ?>
    </p>

    <?= $this->modal->confirmButtons(
        'ColumnRestrictionController',
        'remove',
        array('project_id' => $project['id'], 'restriction_id' => $restriction['restriction_id'])
    ) ?>
</div>
