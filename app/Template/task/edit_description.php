<div class="page-header">
    <h2><?= t('Edit the description') ?></h2>
</div>

<form method="post" action="<?= $this->u('task', 'description', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'ajax' => $ajax)) ?>" autocomplete="off">

    <?= $this->formCsrf() ?>
    <?= $this->formHidden('id', $values) ?>

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
            <?= $this->formTextarea('description', $values, $errors, array('autofocus', 'placeholder="'.t('Leave a description').'"'), 'description-textarea') ?>
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
            <?= $this->a(t('cancel'), 'board', 'show', array('project_id' => $task['project_id'])) ?>
        <?php else: ?>
            <?= $this->a(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        <?php endif ?>
    </div>
</form>
