<div class="page-header">
    <h2><?= t('Edit custom filter') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('customfilter', 'update', array('project_id' => $custom_filter['project_id'], 'user_id' => $custom_filter['user_id'], 'filter' => $custom_filter['filter'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('user_id', $values) ?>
    <?= $this->form->hidden('project_id', $values) ?>
    <?= $this->form->hidden('filter_original', $values) ?>

    <?= $this->form->label(t('Name'), 'name') ?>
    <?= $this->form->text('name', $values, $errors, array('autofocus', 'required', 'maxlength="80"')) ?>
    
    <?= $this->form->label(t('Filter'), 'filter') ?>
    <?= $this->form->text('filter', $values, $errors, array('autofocus', 'required', 'maxlength="80"')) ?>
    
    <?= $this->form->checkbox('is_shared', t('Share with other Members'), 1, $values['is_shared'] == 1) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'customfilter', 'index', array('project_id' => $project['id'])) ?>
    </div>
</form>