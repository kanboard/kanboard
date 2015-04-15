<div class="page-header">
    <h2><?= t('Link modification') ?></h2>
</div>

<form action="<?= $this->u('link', 'update', array('link_id' => $link['id'])) ?>" method="post" autocomplete="off">

    <?= $this->formCsrf() ?>
    <?= $this->formHidden('id', $values) ?>

    <?= $this->formLabel(t('Label'), 'label') ?>
    <?= $this->formText('label', $values, $errors, array('required')) ?>

    <?= $this->formLabel(t('Opposite label'), 'opposite_id') ?>
    <?= $this->formSelect('opposite_id', $labels, $values, $errors) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= $this->a(t('cancel'), 'link', 'index') ?>
    </div>
</form>