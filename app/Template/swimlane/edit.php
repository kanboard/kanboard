<div class="page-header">
    <h2><?= t('Swimlane modification for the project "%s"', $project['name']) ?></h2>
</div>

<form method="post" action="<?= $this->url->href('swimlane', 'update', array('project_id' => $project['id'], 'swimlane_id' => $values['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('id', $values) ?>
    <?= $this->form->hidden('project_id', $values) ?>

    <?= $this->form->label(t('Name'), 'name') ?>
    <?= $this->form->text('name', $values, $errors, array('autofocus', 'required', 'maxlength="50"')) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'swimlane', 'index', array('project_id' => $project['id'])) ?>
    </div>
</form>