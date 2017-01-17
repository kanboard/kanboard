<div class="page-header">
    <h2><?= t('Remove a link') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this link: "%s"?', $link['title']) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'TaskExternalLinkController',
        'remove',
        array('link_id' => $link['id'], 'task_id' => $task['id'], 'project_id' => $task['project_id'])
    ) ?>
</div>
