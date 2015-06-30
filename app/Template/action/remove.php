<div class="page-header">
    <h2><?= t('Remove an automatic action') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this action: "%s"?', $this->text->in($action['event_name'], $available_events).'/'.$this->text->in($action['action_name'], $available_actions)) ?>
    </p>

    <div class="form-actions">
        <?= $this->url->link(t('Yes'), 'action', 'remove', array('project_id' => $project['id'], 'action_id' => $action['id']), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'action', 'index', array('project_id' => $project['id'])) ?>
    </div>
</div>