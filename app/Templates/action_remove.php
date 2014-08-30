<div class="page-header">
    <h2><?= t('Remove an automatic action') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this action: "%s"?', Helper\in_list($action['event_name'], $available_events).'/'.Helper\in_list($action['action_name'], $available_actions)) ?>
    </p>

    <div class="form-actions">
        <a href="?controller=action&amp;action=remove&amp;action_id=<?= $action['id'].Helper\param_csrf() ?>" class="btn btn-red"><?= t('Yes') ?></a>
        <?= t('or') ?> <a href="?controller=action&amp;action=index&amp;project_id=<?= $action['project_id'] ?>"><?= t('cancel') ?></a>
    </div>
</div>