<div class="page-header">
    <h2><?= t('Clone this project') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Which parts of the project do you want to duplicate?') ?>
    </p>
    <form method="post" action="<?= $this->u('project', 'duplicate', array('project_id' => $project['id'], 'duplicate' => 'yes')) ?>" autocomplete="off">

        <?= $this->formCsrf() ?>

        <?= $this->formCheckbox('category', t('Categories'), 1, true) ?>
        <?= $this->formCheckbox('action', t('Actions'), 1, true) ?>
        <?= $this->formCheckbox('swimlane', t('Swimlanes'), 1, false) ?>
        <?= $this->formCheckbox('task', t('Tasks'), 1, false) ?>

        <div class="form-actions">
            <input type="submit" value="<?= t('Duplicate') ?>" class="btn btn-red"/>
            <?= t('or') ?> <?= $this->a(t('cancel'), 'project', 'show', array('project_id' => $project['id'])) ?>
        </div>
    </form>
</div>