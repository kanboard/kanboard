<div class="page-header">
    <h2><?= t('Edit the description') ?></h2>
</div>

<form method="post" action="?controller=task&amp;action=description&amp;task_id=<?= $task['id'] ?>&amp;ajax=<?= $ajax ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>
    <?= Helper\form_hidden('id', $values) ?>

    <div class="form-tabs">
        <ul class="form-tabs-nav">
            <li class="form-tab form-tab-selected">
                <i class="fa fa-pencil-square-o fa-fw"></i><a id="markdown-write" href="#"><?= t('Write') ?></a>
            </li>
            <li class="form-tab">
                <a id="markdown-preview" href="#"><i class="fa fa-eye fa-fw"></i><?= t('Preview') ?></a>
            </li>
        </ul>
        <div class="write-area">
            <?= Helper\form_textarea('description', $values, $errors, array('autofocus', 'placeholder="'.t('Leave a description').'"'), 'description-textarea') ?>
        </div>
        <div class="preview-area">
            <div class="markdown"></div>
        </div>
    </div>

    <div class="form-help"><a href="http://kanboard.net/documentation/syntax-guide" target="_blank" rel="noreferrer"><?= t('Write your text in Markdown') ?></a></div>

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
