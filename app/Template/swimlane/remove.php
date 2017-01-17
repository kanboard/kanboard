<div class="page-header">
    <h2><?= t('Remove a swimlane') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this swimlane: "%s"?', $swimlane['name']) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'SwimlaneController',
        'remove',
        array('project_id' => $project['id'], 'swimlane_id' => $swimlane['id'])
    ) ?>
</div>
