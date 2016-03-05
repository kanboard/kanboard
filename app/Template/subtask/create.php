<div class="page-header">
    <h2><?= t('Add a sub-task') ?></h2>
</div>

<form class="popover-form" method="post" action="<?= $this->url->href('subtask', 'save', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('task_id', $values) ?>
    <?= $this->subtask->selectTitle($values, $errors, array('autofocus')) ?>
    <?= $this->subtask->selectAssignee($users_list, $values, $errors) ?>
    <?= $this->subtask->selectTimeEstimated($values, $errors) ?>

    <?= $this->form->checkbox('another_subtask', t('Create another sub-task'), 1, isset($values['another_subtask']) && $values['another_subtask'] == 1) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'close-popover') ?>
    </div>
</form>
