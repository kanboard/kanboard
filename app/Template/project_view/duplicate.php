<div class="page-header">
    <h2><?= t('Clone this project') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Which parts of the project do you want to duplicate?') ?>
    </p>
    <form method="post" action="<?= $this->url->href('ProjectViewController', 'doDuplication', array('project_id' => $project['id'], 'duplicate' => 'yes')) ?>" autocomplete="off">

        <?= $this->form->csrf() ?>

        <?php if ($project['is_private'] == 0): ?>
            <?= $this->form->checkbox('projectPermissionModel', t('Permissions'), 1, true) ?>
        <?php endif ?>

        <?= $this->form->checkbox('categoryModel', t('Categories'), 1, true) ?>
        <?= $this->form->checkbox('tagDuplicationModel', t('Tags'), 1, true) ?>
        <?= $this->form->checkbox('actionModel', t('Actions'), 1, true) ?>
        <?= $this->form->checkbox('projectMetadataModel', t('Metadata'), 1, false) ?>
        <?= $this->form->checkbox('projectTaskDuplicationModel', t('Tasks'), 1, false) ?>

        <div class="form-actions">
            <button type="submit" class="btn btn-red"><?= t('Duplicate') ?></button>
            <?= t('or') ?> <?= $this->url->link(t('cancel'), 'ProjectViewController', 'show', array('project_id' => $project['id'])) ?>
        </div>
    </form>
</div>
