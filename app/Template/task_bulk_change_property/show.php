<div class="page-header">
    <h2><?= t('Edit tasks in bulk') ?></h2>
</div>

<form action="<?= $this->url->href('TaskBulkChangePropertyController', 'save', ['project_id' => $project['id']]) ?>" method="post">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('task_ids', $values) ?>

    <p class="form-help"><?= t('Choose the properties that you would like to change for the selected tasks.') ?></p>

    <fieldset class="bulk-change-block">
        <div class="bulk-change-checkbox">
            <input type="checkbox" name="change_color" value="1">
        </div>
        <div class="bulk-change-inputs">
            <?= $this->task->renderColorField($values) ?>
        </div>
    </fieldset>

    <fieldset class="bulk-change-block">
        <div class="bulk-change-checkbox">
            <input type="checkbox" name="change_assignee" value="1">
        </div>
        <div class="bulk-change-inputs">
            <?= $this->task->renderAssigneeField($users_list, $values, $errors) ?>
        </div>
    </fieldset>

    <fieldset class="bulk-change-block">
        <div class="bulk-change-checkbox">
            <input type="checkbox" name="change_priority" value="1">
        </div>
        <div class="bulk-change-inputs">
            <?= $this->task->renderPriorityField($project, $values) ?>
        </div>
    </fieldset>

    <fieldset class="bulk-change-block">
        <div class="bulk-change-checkbox">
            <input type="checkbox" name="change_category" value="1">
        </div>
        <div class="bulk-change-inputs">
            <?= $this->task->renderCategoryField($categories_list, $values, $errors, [], true) ?>
        </div>
    </fieldset>

    <fieldset class="bulk-change-block">
        <div class="bulk-change-checkbox">
            <input type="checkbox" name="change_tags" value="1">
        </div>
        <div class="bulk-change-inputs">
            <?= $this->task->renderTagField($project) ?>
        </div>
    </fieldset>

    <fieldset class="bulk-change-block">
        <div class="bulk-change-checkbox">
            <input type="checkbox" name="change_due_date" value="1">
        </div>
        <div class="bulk-change-inputs">
            <?= $this->task->renderDueDateField($values, $errors) ?>
        </div>
    </fieldset>

    <fieldset class="bulk-change-block">
        <div class="bulk-change-checkbox">
            <input type="checkbox" name="change_start_date" value="1">
        </div>
        <div class="bulk-change-inputs">
            <?= $this->task->renderStartDateField($values, $errors) ?>
        </div>
    </fieldset>

    <fieldset class="bulk-change-block">
        <div class="bulk-change-checkbox">
            <input type="checkbox" name="change_estimated_time" value="1">
        </div>
        <div class="bulk-change-inputs">
            <?= $this->task->renderTimeEstimatedField($values, $errors) ?>
        </div>
    </fieldset>

    <fieldset class="bulk-change-block">
        <div class="bulk-change-checkbox">
            <input type="checkbox" name="change_spent_time" value="1">
        </div>
        <div class="bulk-change-inputs">
            <?= $this->task->renderTimeSpentField($values, $errors) ?>
        </div>
    </fieldset>

    <fieldset class="bulk-change-block">
        <div class="bulk-change-checkbox">
            <input type="checkbox" name="change_score" value="1">
        </div>
        <div class="bulk-change-inputs">
            <?= $this->task->renderScoreField($values, $errors) ?>
        </div>
    </fieldset>

    <?= $this->modal->submitButtons() ?>
</form>
