<div class="page-header">
    <h2><?= t('Add a comment') ?></h2>
</div>
<form class="popover-form" method="post" action="<?= $this->url->href('comment', 'save', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('task_id', $values) ?>
    <?= $this->form->hidden('user_id', $values) ?>

    <div class="markdown-editor-small">
        <?= $this->form->textarea(
            'comment',
            $values,
            $errors,
            array(
                'autofocus',
                'required',
                'placeholder="'.t('Leave a comment').'"',
                'data-mention-search-url="'.$this->url->href('UserHelper', 'mention', array('project_id' => $task['project_id'])).'"',
            ),
            'markdown-editor'
        ) ?>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'close-popover') ?>
    </div>
</form>
