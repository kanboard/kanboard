<div class="page-header">
    <h2><?= t('Add a new filter') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('customfilter', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('project_id', $values) ?>
    <?= $this->form->hidden('user_id', $values) ?>

    <?= $this->form->label(t('Name'), 'name') ?>
    <?= $this->form->text('name', $values, $errors, array('autofocus', 'required', 'maxlength="80"')) ?>
    
    <?= $this->form->label(t('Filter'), 'filter') ?>
    <?= $this->form->text('filter', $values, $errors, array('autofocus', 'required', 'maxlength="80"')) ?>
    
    <?php if ($this->user->isProjectManagementAllowed($project['id'])): ?>
        <?= $this->form->checkbox('is_shared', t('Share with all Projectmembers'), 1, 0) ?>
    <?php endif ?>
    
    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>