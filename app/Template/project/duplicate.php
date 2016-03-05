<div class="page-header">
    <h2><?= t('Clone this project') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Which parts of the project do you want to duplicate?') ?>
    </p>
    <form method="post" action="<?= $this->url->href('project', 'duplicate', array('project_id' => $project['id'], 'duplicate' => 'yes')) ?>" autocomplete="off">

        <?= $this->form->csrf() ?>

        <?php if ($project['is_private'] == 0): ?>
            <?= $this->form->checkbox('projectPermission', t('Permissions'), 1, true) ?>
        <?php endif ?>

        <?= $this->form->checkbox('category', t('Categories'), 1, true) ?>
        <?= $this->form->checkbox('action', t('Actions'), 1, true) ?>
        <?= $this->form->checkbox('swimlane', t('Swimlanes'), 1, false) ?>
        <?= $this->form->checkbox('task', t('Tasks'), 1, false) ?>

        <div class="form-actions">
            <button type="submit" class="btn btn-red"><?= t('Duplicate') ?></button>
            <?= t('or') ?> <?= $this->url->link(t('cancel'), 'project', 'show', array('project_id' => $project['id'])) ?>
        </div>
    </form>
</div>