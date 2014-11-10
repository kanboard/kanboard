<div class="page-header">
    <h2><?= t('Remove an automatic action') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this action: "%s"?', Helper\in_list($action['event_name'], $available_events).'/'.Helper\in_list($action['action_name'], $available_actions)) ?>
    </p>

    <div class="form-actions">
        <?= Helper\a(t('Yes'), 'action', 'remove', array('project_id' => $project['id'], 'action_id' => $action['id']), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?= Helper\a(t('cancel'), 'action', 'index', array('project_id' => $project['id'])) ?>
    </div>
</div>