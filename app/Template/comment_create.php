<div class="page-header">
    <h2><?= t('Add a comment') ?></h2>
</div>

<form method="post" action="?controller=comment&amp;action=save&amp;task_id=<?= $task['id'] ?>" autocomplete="off">
    <?= Helper\form_csrf() ?>
    <?= Helper\form_hidden('task_id', $values) ?>
    <?= Helper\form_hidden('user_id', $values) ?>
    <?= Helper\form_textarea('comment', $values, $errors, array(! isset($skip_cancel) ? 'autofocus' : '', 'required', 'placeholder="'.t('Leave a comment').'"'), 'comment-textarea') ?><br/>
    <div class="form-help"><a href="http://kanboard.net/documentation/syntax-guide" target="_blank" rel="noreferrer"><?= t('Write your text in Markdown') ?></a></div>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?php if (! isset($skip_cancel)): ?>
            <?= t('or') ?>
            <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>"><?= t('cancel') ?></a>
        <?php endif ?>
    </div>
</form>
