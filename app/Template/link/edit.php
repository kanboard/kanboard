<div class="page-header">
    <h2><?= t('Link modification for the project "%s"', $project['name']) ?></h2>
</div>

<form method="post" action="<?= Helper\u('link', 'update', array('project_id' => $project['id'], 'link_id' => $values['id'])) ?>" autocomplete="off">
	<div class="alert alert-info">
		<strong><?= t('Example:') ?></strong>
		<i><?= t('#10 Follows #9') ?></i>
		<?= t('and therefore') ?>
		<i><?= t('#9 Precedes #10') ?></i>
	</div>
    <?= Helper\form_csrf() ?>

    <?= Helper\form_hidden('id', $values) ?>
    <?= Helper\form_hidden('project_id', $values) ?>

    <?= Helper\form_label(t('Link Name'), 'name') ?>
    <?= Helper\form_text('name', $values, $errors, array('required', 'autofocus', 'placeholder="Precedes"')) ?>

    <?= Helper\form_label(t('Link Inverse Name'), 'name_inverse') ?>
    <?= Helper\form_text('name_inverse', $values, $errors, array('required', 'placeholder="Follows"')) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>