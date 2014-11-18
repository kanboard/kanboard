<div class="page-header">
    <h2><?= t('Edit the description') ?></h2>
</div>

<form method="post" action="?controller=task&amp;action=description&amp;task_id=<?= $task['id'] ?>&amp;ajax=<?= $ajax ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>

    <?= Helper\form_hidden('id', $values) ?>
    <div class="form-tabs">
        <div class="form-tabs-nav">
            <a id="w" class="form-tab form-tab-selected btn btn-small" href="#w"><?= t('Write') ?></a>
            <a id="p" class="form-tab btn btn-small" href="#p"><?= t('Preview') ?></a>
            <span class="form-required">*</span>
            <span class="form-help pull-right"><a href="http://kanboard.net/documentation/syntax-guide" target="_blank" rel="noreferrer"><i class="octicon octicon-markdown"></i> <?= t('Markdown supported') ?></a></span>
        </div>
        <div class="write-area form-tab">
            <?= Helper\form_textarea('description', $values, $errors, array('autofocus', 'required', 'placeholder="'.t('Leave a description').'"'), 'description-textarea') ?><br/>
        </div>
        <div class="preview-area form-tab">
            <div class="markdown"></div>
        </div>
    </div>


    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?php if ($ajax): ?>
                <a href="?controller=board&amp;action=show&amp;project_id=<?= $task['project_id'] ?>"><?= t('cancel') ?></a>
        <?php else: ?>
                <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>"><?= t('cancel') ?></a>
        <?php endif ?>
    </div>
</form>
