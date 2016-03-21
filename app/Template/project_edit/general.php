<div class="page-header">
    <h2><?= t('Edit project') ?></h2>
    <ul>
        <li class="active"><?= $this->url->link(t('General'), 'ProjectEdit', 'edit', array('project_id' => $project['id']), false, 'popover-link') ?></li>
        <li><?= $this->url->link(t('Dates'), 'ProjectEdit', 'dates', array('project_id' => $project['id']), false, 'popover-link') ?></li>
        <li><?= $this->url->link(t('Description'), 'ProjectEdit', 'description', array('project_id' => $project['id']), false, 'popover-link') ?></li>
        <li><?= $this->url->link(t('Task priority'), 'ProjectEdit', 'priority', array('project_id' => $project['id']), false, 'popover-link') ?></li>
    </ul>
</div>
<form method="post" class="popover-form" action="<?= $this->url->href('ProjectEdit', 'update', array('project_id' => $project['id'], 'redirect' => 'edit')) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('id', $values) ?>

    <?= $this->form->label(t('Name'), 'name') ?>
    <?= $this->form->text('name', $values, $errors, array('required', 'maxlength="50"')) ?>

    <?= $this->form->label(t('Identifier'), 'identifier') ?>
    <?= $this->form->text('identifier', $values, $errors, array('maxlength="50"')) ?>
    <p class="form-help"><?= t('The project identifier is optional and must be alphanumeric, example: MYPROJECT.') ?></p>

    <hr>
    <div class="form-inline">
        <?= $this->form->label(t('Project owner'), 'owner_id') ?>
        <?= $this->form->select('owner_id', $owners, $values, $errors) ?>
    </div>

    <?php if ($this->user->hasProjectAccess('ProjectCreation', 'create', $project['id'])): ?>
        <hr>
        <?= $this->form->checkbox('is_private', t('Private project'), 1, $project['is_private'] == 1) ?>
        <p class="form-help"><?= t('Private projects do not have users and groups management.') ?></p>
    <?php endif ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
    </div>
</form>
