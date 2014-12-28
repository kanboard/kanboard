<div class="page-header">
    <h2><?= t('Remove an automatic action') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this action: "%s"?', $this->inList($action['event_name'], $available_events).'/'.$this->inList($action['action_name'], $available_actions)) ?>
    </p>

    <div class="form-actions">
        <?= $this->a(t('Yes'), 'action', 'remove', array('project_id' => $project['id'], 'action_id' => $action['id']), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?= $this->a(t('cancel'), 'action', 'index', array('project_id' => $project['id'])) ?>
    </div>
</div>