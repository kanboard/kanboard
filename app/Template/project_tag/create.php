<div class="page-header">
    <h2><?= t('Add new tag') ?></h2>
</div>
<form method="post" class="popover-form" action="<?= $this->url->href('ProjectTagController', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('project_id', $values) ?>

    <?= $this->form->label(t('Name'), 'name') ?>
    <?= $this->form->text('name', $values, $errors, array('autofocus', 'required', 'maxlength="255"')) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'ProjectTagController', 'index', array('project_id' => $project['id']), false, 'close-popover') ?>
    </div>
</form>
