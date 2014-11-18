<div class="page-header">
    <h2><?= t('Add a comment') ?></h2>
</div>

<form method="post" action="?controller=comment&amp;action=save&amp;task_id=<?= $task['id'] ?>" autocomplete="off">
    <?= Helper\form_csrf() ?>
    <?= Helper\form_hidden('task_id', $values) ?>
    <?= Helper\form_hidden('user_id', $values) ?>
    <div class="form-tabs">
        <div class="form-tabs-nav">
            <a id="w" class="form-tab form-tab-selected btn btn-small" href="#w"><?= t('Write') ?></a>
            <a id="p" class="form-tab btn btn-small" href="#p"><?= t('Preview') ?></a>
            <span class="form-required">*</span>
            <span class="form-help pull-right"><a href="http://kanboard.net/documentation/syntax-guide" target="_blank" rel="noreferrer"><i class="octicon octicon-markdown"></i> <?= t('Markdown supported') ?></a></span>
        </div>
        <div class="write-area form-tab">
            <?= Helper\form_textarea('comment', $values, $errors, array(! isset($skip_cancel) ? 'autofocus' : '', 'required', 'placeholder="'.t('Leave a comment').'"'), 'comment-textarea') ?>
        </div>
        <div class="preview-area form-tab">
            <div class="comment-inner">
                <div class="markdown"></div>
            </div>
        </div>
    </div>
    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?php if (! isset($skip_cancel)): ?>
            <?= t('or') ?>
            <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>"><?= t('cancel') ?></a>
        <?php endif ?>
    </div>
</form>
