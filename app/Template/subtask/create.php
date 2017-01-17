<div class="page-header">
    <h2><?= t('Add a sub-task') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('SubtaskController', 'save', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('task_id', $values) ?>
    <?= $this->subtask->renderTitleField($values, $errors, array('autofocus')) ?>
    <?= $this->subtask->renderAssigneeField($users_list, $values, $errors) ?>
    <?= $this->subtask->renderTimeEstimatedField($values, $errors) ?>

    <?= $this->hook->render('template:subtask:form:create', array('values' => $values, 'errors' => $errors)) ?>
    
    <?= $this->form->checkbox('another_subtask', t('Create another sub-task'), 1, isset($values['another_subtask']) && $values['another_subtask'] == 1) ?>

    <?= $this->modal->submitButtons() ?>
</form>
