<div class="page-header">
    <h2><?= t('Edit a sub-task') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('SubtaskController', 'update', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>

    <?= $this->subtask->renderTitleField($values, $errors, array('autofocus')) ?>
    <?= $this->subtask->renderAssigneeField($users_list, $values, $errors) ?>
    <?= $this->subtask->renderTimeEstimatedField($values, $errors) ?>
    <?= $this->subtask->renderTimeSpentField($values, $errors) ?>

    <?= $this->hook->render('template:subtask:form:edit', array('values' => $values, 'errors' => $errors)) ?>

    <?= $this->modal->submitButtons() ?>
</form>
