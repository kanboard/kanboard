<div class="page-header">
    <h2><?= t('Edit a comment') ?></h2>
</div>

<form method="post" action="<?= Helper\u('comment', 'update', array('task_id' => $task['id'], 'comment_id' => $comment['id'])) ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>
    <?= Helper\form_hidden('id', $values) ?>
    <?= Helper\form_hidden('task_id', $values) ?>

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
            <?= Helper\form_textarea('comment', $values, $errors, array('autofocus', 'required', 'placeholder="'.t('Leave a comment').'"'), 'comment-textarea') ?>
        </div>
        <div class="preview-area">
            <div class="markdown"></div>
        </div>
    </div>

    <div class="form-help"><a href="http://kanboard.net/documentation/syntax-guide" target="_blank" rel="noreferrer"><?= t('Write your text in Markdown') ?></a></div>

    <div class="form-actions">
        <input type="submit" value="<?= t('Update') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= Helper\a(t('cancel'), 'task', 'show', array('task_id' => $task['id'])) ?>
    </div>
</form>
