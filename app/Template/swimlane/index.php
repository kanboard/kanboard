<?php if (! empty($active_swimlanes)): ?>
<div class="page-header">
    <h2><?= t('Active swimlanes') ?></h2>
</div>
<?= $this->render('swimlane/table', array('swimlanes' => $active_swimlanes, 'project' => $project)) ?>
<?php endif ?>

<div class="page-header">
    <h2><?= t('Add a new swimlane') ?></h2>
</div>
<form method="post" action="<?= $this->u('swimlane', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= $this->formCsrf() ?>
    <?= $this->formHidden('project_id', $values) ?>

    <?= $this->formLabel(t('Name'), 'name') ?>
    <?= $this->formText('name', $values, $errors, array('autofocus', 'required', 'maxlength="50"')) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>

<div class="page-header">
    <h2><?= t('Change default swimlane') ?></h2>
</div>
<form method="post" action="<?= $this->u('swimlane', 'change', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= $this->formCsrf() ?>
    <?= $this->formHidden('id', $default_swimlane) ?>

    <?= $this->formLabel(t('Rename'), 'default_swimlane') ?>
    <?= $this->formText('default_swimlane', $default_swimlane, array(), array('autofocus', 'required', 'maxlength="50"')) ?><br/>

    <?= $this->formCheckbox('show_default_swimlane', t('Show default swimlane'), 1, isset($default_swimlane['show_default_swimlane']) && $default_swimlane['show_default_swimlane'] == 1) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>

<?php if (! empty($inactive_swimlanes)): ?>
<div class="page-header">
    <h2><?= t('Inactive swimlanes') ?></h2>
</div>
<?= $this->render('swimlane/table', array('swimlanes' => $inactive_swimlanes, 'project' => $project, 'hide_position' => true)) ?>
<?php endif ?>