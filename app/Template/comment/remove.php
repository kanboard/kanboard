<div class="page-header">
    <h2><?= t('Remove a comment') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this comment?') ?>
    </p>

    <?= Helper\template('comment/show', array('comment' => $comment, 'task' => $task, 'preview' => true)) ?>

    <div class="form-actions">
        <?= Helper\a(t('Yes'), 'comment', 'remove', array('task_id' => $task['id'], 'comment_id' => $comment['id']), true, 'btn btn-red') ?>
        <?= t('or') ?>
        <?= Helper\a(t('cancel'), 'task', 'show', array('task_id' => $task['id'])) ?>
    </div>
</div>