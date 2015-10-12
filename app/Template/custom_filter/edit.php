<div class="page-header">
    <h2><?= t('Edit custom filter') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('customfilter', 'update', array('project_id' => $filter['project_id'], 'filter_id' => $filter['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('id', $values) ?>
    <?= $this->form->hidden('user_id', $values) ?>
    <?= $this->form->hidden('project_id', $values) ?>

    <?= $this->form->label(t('Name'), 'name') ?>
    <?= $this->form->text('name', $values, $errors, array('autofocus', 'required', 'maxlength="100"')) ?>

    <?= $this->form->label(t('Filter'), 'filter') ?>
    <?= $this->form->text('filter', $values, $errors, array('required', 'maxlength="100"')) ?>

    <?php if ($this->user->isProjectManagementAllowed($project['id'])): ?>
        <?= $this->form->checkbox('is_shared', t('Share with all project members'), 1, $values['is_shared'] == 1) ?>
    <?php else: ?>
        <?= $this->form->hidden('is_shared', $values) ?>
    <?php endif ?>
    
    <?= $this->form->checkbox('append', t('Append filter (instead of replacement)'), 1, $values['append'] == 1) ?>
    
    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue">
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'customfilter', 'index', array('project_id' => $project['id'])) ?>
    </div>
</form>