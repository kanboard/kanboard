<div class="page-header">
    <h2><?= t('Category modification for the project "%s"', $project['name']) ?></h2>
</div>

<form method="post" action="<?= $this->u('category', 'update', array('project_id' => $project['id'], 'category_id' => $values['id'])) ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

    <?= $this->formHidden('id', $values) ?>
    <?= $this->formHidden('project_id', $values) ?>

    <?= $this->formLabel(t('Category Name'), 'name') ?>
    <?= $this->formText('name', $values, $errors, array('autofocus', 'required', 'maxlength="50"')) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>