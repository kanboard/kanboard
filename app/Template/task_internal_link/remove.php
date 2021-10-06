<div class="page-header">
    <h2><?= t('Remove a link') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this link with task #%d?', $link['opposite_task_id']) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'TaskInternalLinkController',
        'remove',
        array('link_id' => $link['id'], 'task_id' => $task['id'])
    ) ?>
</div>
