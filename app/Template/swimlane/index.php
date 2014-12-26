<?php if (! empty($active_swimlanes)): ?>
<div class="page-header">
    <h2><?= t('Active swimlanes') ?></h2>
</div>
<?= Helper\template('swimlane/table', array('swimlanes' => $active_swimlanes, 'project' => $project)) ?>
<?php endif ?>

<div class="page-header">
    <h2><?= t('Add a new swimlane') ?></h2>
</div>
<form method="post" action="<?= Helper\u('swimlane', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>
    <?= Helper\form_hidden('project_id', $values) ?>

    <?= Helper\form_label(t('Name'), 'name') ?>
    <?= Helper\form_text('name', $values, $errors, array('autofocus required')) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>

<div class="page-header">
    <h2><?= t('Change default swimlane') ?></h2>
</div>
<form method="post" action="<?= Helper\u('swimlane', 'change', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>
    <?= Helper\form_hidden('id', $default_swimlane) ?>

    <?= Helper\form_label(t('Rename'), 'default_swimlane') ?>
    <?= Helper\form_text('default_swimlane', $default_swimlane, array(), array('autofocus required')) ?><br/>

    <?= Helper\form_checkbox('show_default_swimlane', t('Show default swimlane'), 1, isset($default_swimlane['show_default_swimlane']) && $default_swimlane['show_default_swimlane'] == 1) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>

<?php if (! empty($inactive_swimlanes)): ?>
<div class="page-header">
    <h2><?= t('Inactive swimlanes') ?></h2>
</div>
<?= Helper\template('swimlane/table', array('swimlanes' => $inactive_swimlanes, 'project' => $project, 'hide_position' => true)) ?>
<?php endif ?>