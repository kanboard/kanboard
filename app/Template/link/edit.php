<section id="link-edit-section">
<?php use Model\Link;
if (! isset($edit)): ?>
    <h3><?= t('Add a new link label') ?></h3>
<?php else: ?>
<div class="page-header">
    <h2><?= t('Edit the link label') ?></h2>
</div>
<?php endif ?>

<form method="post" action="<?= $this->u('link', isset($edit) ? 'update' : 'save', array('project_id' => $project['id'], 'link_id' => @$values['id'])) ?>" autocomplete="off">
    <?= $this->formCsrf() ?>

    <?php if (isset($edit)): ?>
        <?= $this->formHidden('link_id', $values) ?>
        <?= $this->formHidden('id[0]', $values[0]) ?>
        <?php if (isset($values[1])): ?>
        <?= $this->formHidden('id[1]', $values[1]) ?>
        <?php endif ?>
    <?php endif ?>
    <?= $this->formHidden('project_id', $values) ?>

    <?= $this->formLabel(t('Link Label'), 'label[0]') ?>
    <?= $this->formText('label[0]', $values[0], $errors, array('required', 'autofocus', 'placeholder="'.t('precedes').'"')) ?> &raquo;
    
    <?= $this->formCheckbox('behaviour[0]', t('Bidrectional link label'), Link::BEHAVIOUR_BOTH, (isset($values[0]['behaviour']) && Link::BEHAVIOUR_BOTH == $values[0]['behaviour']), 'behaviour') ?>

    <div class="link-inverse-label">
    <?= $this->formLabel(t('Link Inverse Label'), 'label[1]') ?>
    &laquo; <?= $this->formText('label[1]', isset($values[1]) ? $values[1] : $values, $errors, array('placeholder="'.t('follows').'"')) ?>
    </div>
    
    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?php if (isset($edit)): ?>
        <?= t('or') ?>
        <?= $this->a(t('cancel'), 'link', 'index', array('project_id' => $project['id'])) ?>
        <?php endif ?>
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
</section>