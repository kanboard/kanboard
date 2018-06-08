<div class="page-header">
    <h2><?= t('Move selected tasks to another column') ?></h2>
</div>

<form action="<?= $this->url->href('TaskBulkMoveColumnController', 'save', ['project_id' => $project['id']]) ?>" method="post">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('task_ids', $values) ?>

    <?= $this->form->label(t('Swimlane'), 'swimlane_id') ?>
    <?= $this->form->select('swimlane_id', $swimlanes, $values) ?>

    <?= $this->form->label(t('Column'), 'column_id') ?>
    <?= $this->form->select('column_id', $columns, $values) ?>

    <?= $this->modal->submitButtons() ?>
</form>
