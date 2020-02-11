<div class="page-header">
    <h2><?= t('Change to global tag') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to make the tag "%s" global?', $tag['name']) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'ProjectTagController',
        'makeGlobalTag',
        array('tag_id' => $tag['id'], 'project_id' => $project['id'])
    ) ?>
</div>
