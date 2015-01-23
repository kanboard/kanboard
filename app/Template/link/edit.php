<?php if (! isset($edit)): ?>
    <h3><?= t('Add a new link label') ?></h3>
<?php else: ?>
<div class="page-header">
    <h2><?= t('Edit the link label') ?></h2>
</div>
<?php endif ?>

<form method="post" action="<?= $this->u('link', isset($edit) ? 'update' : 'save', array('project_id' => $project['id'], 'link_id' => @$values['id'])) ?>" autocomplete="off">
    <?= $this->formCsrf() ?>

    <?php if (isset($edit)): ?>
    <?= $this->formHidden('id', $values) ?>
    <?php endif ?>
    <?= $this->formHidden('project_id', $values) ?>

    <?= $this->formLabel(t('Link Label'), 'name') ?>
    <?= $this->formText('name', $values, $errors, array('required', 'autofocus', 'placeholder="'.t('precedes').'"')) ?>

    <?= $this->formLabel(t('Link Inverse Label'), 'name_inverse') ?>
    <?= $this->formText('name_inverse', $values, $errors, array('required', 'placeholder="'.t('follows').'"')) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
    <?php if (! isset($edit)): ?>
    <div class="alert alert-info">
        <strong><?= t('Example:') ?></strong>
        <i><?= t('#9 precedes #10') ?></i>
        <?= t('and therefore') ?>
        <i><?= t('#10 follows #9') ?></i>
    </div>
    <?php endif ?>
</form>
