<div class="page-header">
    <h2><?= $this->text->e($project['name']) ?> &gt; <?= t('Create tasks in bulk') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('TaskBulkController', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('column_id', $values) ?>
    <?= $this->form->hidden('swimlane_id', $values) ?>

    <?php if (! empty($task_description_templates)): ?>
        <?= $this->form->label(t('Template for the task description'), 'task_description_template_id') ?>
        <?= $this->form->select('task_description_template_id', $task_description_templates, $values, $errors) ?>
    <?php endif ?>

    <?= $this->form->label(t('Tasks'), 'tasks') ?>
    <?= $this->form->textarea('tasks', $values, $errors, array('placeholder="'.t('My task title').'"')) ?>
    <p class="form-help"><?= t('Enter one task by line.') ?></p>

    <?= $this->task->renderColorField($values) ?>
    <?= $this->task->renderAssigneeField($users_list, $values, $errors) ?>
    <?= $this->task->renderPriorityField($project, $values) ?>
    <?= $this->task->renderDueDateField($values, $errors) ?>
    <?= $this->task->renderTimeEstimatedField($values, $errors) ?>
    <?= $this->task->renderScoreField($values, $errors) ?>
    <?= $this->task->renderCategoryField($categories_list, $values, $errors) ?>
    <?= $this->task->renderTagField($project) ?>

    <?= $this->modal->submitButtons() ?>
</form>

