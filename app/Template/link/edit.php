<div class="page-header">
    <h2><?= t('Link modification for the project "%s"', $project['name']) ?></h2>
</div>

<form method="post" action="<?= $this->u('link', 'update', array('project_id' => $project['id'], 'link_id' => $values['id'])) ?>" autocomplete="off">
	<div class="alert alert-info">
		<strong><?= t('Example:') ?></strong>
		<i><?= t('#9 Precedes #10') ?></i>
		<?= t('and therefore') ?>
		<i><?= t('#10 Follows #9') ?></i>
	</div>
    <?= $this->formCsrf() ?>

    <?= $this->formHidden('id', $values) ?>
    <?= $this->formHidden('project_id', $values) ?>

    <?= $this->formLabel(t('Link Label'), 'name') ?>
    <?= $this->formText('name', $values, $errors, array('required', 'autofocus', 'placeholder="Precedes"')) ?>

    <?= $this->formLabel(t('Link Inverse Label'), 'name_inverse') ?>
    <?= $this->formText('name_inverse', $values, $errors, array('required', 'placeholder="Follows"')) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>