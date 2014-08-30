<div class="page-header">
    <h2><?= t('Edit project') ?></h2>
</div>
<form method="post" action="?controller=project&amp;action=update&amp;project_id=<?= $values['id'] ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>
    <?= Helper\form_hidden('id', $values) ?>

    <?= Helper\form_label(t('Name'), 'name') ?>
    <?= Helper\form_text('name', $values, $errors, array('required')) ?>

    <?= Helper\form_checkbox('is_active', t('Activated'), 1, isset($values['is_active']) && $values['is_active'] == 1) ?><br/>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>