<div class="page-header">
    <h2><?= t('Edit a sub-task') ?></h2>
</div>

<form class="popover-form" method="post" action="<?= $this->url->href('subtask', 'update', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('id', $values) ?>
    <?= $this->form->hidden('task_id', $values) ?>
    <?= $this->subtask->selectTitle($values, $errors, array('autofocus')) ?>
    <?= $this->subtask->selectAssignee($users_list, $values, $errors) ?>
    <?= $this->subtask->selectTimeEstimated($values, $errors) ?>
    <?= $this->subtask->selectTimeSpent($values, $errors) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'close-popover') ?>
    </div>
</form>
