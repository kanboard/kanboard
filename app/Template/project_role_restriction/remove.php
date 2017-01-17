<div class="page-header">
    <h2><?= t('Remove a project restriction') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this project restriction: "%s"?', $this->text->in($restriction['rule'], $restrictions)) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'ProjectRoleRestrictionController',
        'remove',
        array('project_id' => $project['id'], 'restriction_id' => $restriction['restriction_id'])
    ) ?>
</div>
