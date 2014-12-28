<div class="page-header">
    <h2><?= t('Swimlane modification for the project "%s"', $project['name']) ?></h2>
</div>

<form method="post" action="<?= $this->u('swimlane', 'update', array('project_id' => $project['id'], 'swimlane_id' => $values['id'])) ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

    <?= $this->formHidden('id', $values) ?>
    <?= $this->formHidden('project_id', $values) ?>

    <?= $this->formLabel(t('Name'), 'name') ?>
    <?= $this->formText('name', $values, $errors, array('autofocus required')) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>