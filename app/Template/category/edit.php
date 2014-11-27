<div class="page-header">
    <h2><?= t('Category modification for the project "%s"', $project['name']) ?></h2>
</div>

<form method="post" action="<?= Helper\u('category', 'update', array('project_id' => $project['id'], 'category_id' => $values['id'])) ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>

    <?= Helper\form_hidden('id', $values) ?>
    <?= Helper\form_hidden('project_id', $values) ?>

    <?= Helper\form_label(t('Category Name'), 'name') ?>
    <?= Helper\form_text('name', $values, $errors, array('autofocus required')) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>