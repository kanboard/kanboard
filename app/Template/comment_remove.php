<div class="page-header">
    <h2><?= t('Remove a comment') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this comment?') ?>
    </p>

    <?= Helper\template('comment_show', array('comment' => $comment, 'task' => $task, 'preview' => true)) ?>

    <div class="form-actions">
        <a href="?controller=comment&amp;action=remove&amp;task_id=<?= $task['id'] ?>&amp;comment_id=<?= $comment['id'].Helper\param_csrf() ?>" class="btn btn-red"><?= t('Yes') ?></a>
        <?= t('or') ?> <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>#comment-<?= $comment['id'] ?>"><?= t('cancel') ?></a>
    </div>
</div>