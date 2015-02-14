<div class="page-header">
    <h2><?= t('Add a new link') ?></h2>
</div>

<form action="<?= $this->u('link', 'save') ?>" method="post" autocomplete="off">

    <?= $this->formCsrf() ?>

    <?= $this->formLabel(t('Label'), 'label') ?>
    <?= $this->formText('label', $values, $errors, array('required')) ?>

    <?= $this->formLabel(t('Opposite label'), 'opposite_label') ?>
    <?= $this->formText('opposite_label', $values, $errors) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>