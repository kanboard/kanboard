<div class="page-header">
    <h2><?= t('Import actions from another project') ?></h2>
</div>
<?php if (empty($projects_list)): ?>
    <p class="alert"><?= t('There is no available project.') ?></p>
<?php else: ?>
    <form class="popover-form" method="post" action="<?= $this->url->href('ActionProject', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">

        <?= $this->form->csrf() ?>

        <?= $this->form->label(t('Create from another project'), 'src_project_id') ?>
        <?= $this->form->select('src_project_id', $projects_list) ?>

        <div class="form-actions">
            <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
            <?= t('or') ?>
            <?= $this->url->link(t('cancel'), 'Action', 'index', array(), false, 'close-popover') ?>
        </div>
    </form>
<?php endif ?>