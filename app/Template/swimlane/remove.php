<section id="main">
    <div class="page-header">
        <h2><?= t('Remove a swimlane') ?></h2>
    </div>

    <div class="confirm">
        <p class="alert alert-info">
            <?= t('Do you really want to remove this swimlane: "%s"?', $swimlane['name']) ?>
        </p>

        <div class="form-actions">
            <?= $this->a(t('Yes'), 'swimlane', 'remove', array('project_id' => $project['id'], 'swimlane_id' => $swimlane['id']), true, 'btn btn-red') ?>
            <?= t('or') ?>
            <?= $this->a(t('cancel'), 'swimlane', 'index', array('project_id' => $project['id'])) ?>
        </div>
    </div>
</section>