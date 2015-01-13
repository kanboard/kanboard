<div class="page-header">
    <h2><?= t('Edit project') ?></h2>
</div>
<form method="post" action="<?= $this->u('project', 'update', array('project_id' => $values['id'])) ?>" autocomplete="off">

    <?= $this->formCsrf() ?>
    <?= $this->formHidden('id', $values) ?>

    <?= $this->formLabel(t('Name'), 'name') ?>
    <?= $this->formText('name', $values, $errors, array('required', 'maxlength="50"')) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>