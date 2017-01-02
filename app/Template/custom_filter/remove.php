<div class="page-header">
    <h2><?= t('Remove a custom filter') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this custom filter: "%s"?', $filter['name']) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'CustomFilterController',
        'remove',
        array('project_id' => $project['id'], 'filter_id' => $filter['id'])
    ) ?>
</div>
