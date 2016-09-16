<div class="page-header">
    <h2><?= t('Remove a custom role') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this custom role: "%s"? All people assigned to this role will become project member.', $role['role']) ?>
    </p>

    <div class="form-actions">
        <?= $this->url->link(t('Yes'), 'ProjectRoleController', 'remove', array('project_id' => $project['id'], 'role_id' => $role['role_id']), true, 'btn btn-red') ?>
        <?= t('or') ?> <?= $this->url->link(t('cancel'), 'ProjectRoleController', 'show', array('project_id' => $project['id']), false, 'close-popover') ?>
    </div>
</div>
