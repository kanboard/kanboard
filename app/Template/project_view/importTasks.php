<div class="page-header">
    <h2><?= t('Import tasks from another project') ?></h2>
</div>
<?php if (count($projects) > 0): ?>
    <form method="post" action="<?= $this->url->href('ProjectViewController', 'doTasksImport', ['project_id' => $project['id']]) ?>">
        <?= $this->form->csrf() ?>

        <?= $this->form->label(t('Select the project to copy tasks from'), 'src_project_id') ?>
        <?= $this->form->select('src_project_id', $projects, $values, $errors) ?>

        <?= $this->modal->submitButtons(['submitLabel' => t('Save')]) ?>
    </form>
<?php else: ?>
    <p class="alert alert-info"><?= t('No other projects found.') ?></p>
<?php endif ?>
