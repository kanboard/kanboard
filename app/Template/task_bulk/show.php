<div class="page-header">
    <h2><?= $this->text->e($project['name']) ?> &gt; <?= t('Create tasks in bulk') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('TaskBulkController', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('column_id', $values) ?>
    <?= $this->form->hidden('swimlane_id', $values) ?>
    <?= $this->form->hidden('project_id', $values) ?>

    <?= $this->task->renderColorField($values) ?>
    <?= $this->task->renderAssigneeField($users_list, $values, $errors) ?>
    <?= $this->task->renderCategoryField($categories_list, $values, $errors) ?>

    <?= $this->form->label(t('Tasks'), 'tasks') ?>
    <?= $this->form->textarea('tasks', $values, $errors, array('placeholder="'.t('My task title').'"')) ?>
    <p class="form-help"><?= t('Enter one task by line.') ?></p>

    <?= $this->modal->submitButtons() ?>
</form>

