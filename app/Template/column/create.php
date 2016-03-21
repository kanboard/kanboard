<div class="page-header">
    <h2><?= t('Add a new column') ?></h2>
</div>
<form class="popover-form" method="post" action="<?= $this->url->href('Column', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('project_id', $values) ?>

    <?= $this->form->label(t('Title'), 'title') ?>
    <?= $this->form->text('title', $values, $errors, array('autofocus', 'required', 'maxlength="50"')) ?>

    <?= $this->form->label(t('Task limit'), 'task_limit') ?>
    <?= $this->form->number('task_limit', $values, $errors) ?>

    <?= $this->form->label(t('Description'), 'description') ?>
    <?= $this->form->textarea('description', $values, $errors, array(), 'markdown-editor') ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'column', 'index', array('project_id' => $project['id']), false, 'close-popover') ?>
    </div>
</form>