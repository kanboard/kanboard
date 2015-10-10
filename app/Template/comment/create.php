<div class="page-header">
    <h2><?= t('Add a comment') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('comment', 'save', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'ajax' => isset($ajax))) ?>" autocomplete="off" class="form-comment">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('task_id', $values) ?>
    <?= $this->form->hidden('user_id', $values) ?>

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
            <?= $this->form->textarea('comment', $values, $errors, array(! isset($skip_cancel) ? 'autofocus' : '', 'required', 'placeholder="'.t('Leave a comment').'"'), 'comment-textarea') ?>
        </div>
        <div class="preview-area">
            <div class="markdown"></div>
        </div>
    </div>

    <div class="form-help"><?= $this->url->doc(t('Write your text in Markdown'), 'syntax-guide') ?></div>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?php if (! isset($skip_cancel)): ?>
            <?= t('or') ?>
            <?php if (isset($ajax)): ?>
                <?= $this->url->link(t('cancel'), 'board', 'show', array('project_id' => $task['project_id'])) ?>
            <?php else: ?>
                <?= $this->url->link(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
            <?php endif ?>
        <?php endif ?>
    </div>
</form>
