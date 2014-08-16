<div class="page-header">
    <h2><?= t('Edit a comment') ?></h2>
</div>

<form method="post" action="?controller=comment&amp;action=update&amp;task_id=<?= $task['id'] ?>&amp;comment_id=<?= $comment['id'] ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>
    <?= Helper\form_hidden('id', $values) ?>
    <?= Helper\form_hidden('task_id', $values) ?>
    <?= Helper\form_textarea('comment', $values, $errors, array('autofocus', 'required', 'placeholder="'.t('Leave a comment').'"'), 'comment-textarea') ?><br/>

    <div class="form-actions">
        <input type="submit" value="<?= t('Update') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>"><?= t('cancel') ?></a>
    </div>
</form>
