<div class="page-header">
    <h2><?= t('Add a sub-task') ?></h2>
</div>

<?php if (isset($values['subtasks_added']) && $values['subtasks_added'] > 0): ?>
    <p class="alert alert-success">
    <?php if ($values['subtasks_added'] == 1): ?>
        <?= t('Subtask added successfully.') ?>
    <?php else: ?>
        <?= t('%d subtasks added successfully.', $values['subtasks_added']) ?>
    <?php endif ?>
    </p>
<?php endif ?>

<form method="post" action="<?= $this->url->href('SubtaskController', 'save', array('task_id' => $task['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>

    <?= $this->subtask->renderBulkTitleField($values, $errors, array('autofocus')) ?>
    <?= $this->subtask->renderAssigneeField($users_list, $values, $errors) ?>
    <?= $this->subtask->renderTimeEstimatedField($values, $errors) ?>

    <?= $this->hook->render('template:subtask:form:create', array('values' => $values, 'errors' => $errors)) ?>

    <?= $this->form->checkbox('another_subtask', t('Create another sub-task'), 1, isset($values['another_subtask']) && $values['another_subtask'] == 1) ?>

    <?= $this->modal->submitButtons() ?>
</form>
