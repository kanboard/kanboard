<div class="page-header">
    <h2><?= t('Add a sub-task') ?></h2>
</div>

<form method="post" action="<?= $this->u('subtask', 'save', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

    <?= $this->formHidden('task_id', $values) ?>

    <?= $this->formLabel(t('Title'), 'title') ?>
    <?= $this->formText('title', $values, $errors, array('required autofocus')) ?><br/>

    <?= $this->formLabel(t('Assignee'), 'user_id') ?>
    <?= $this->formSelect('user_id', $users_list, $values, $errors) ?><br/>

    <?= $this->formLabel(t('Original estimate'), 'time_estimated') ?>
    <?= $this->formNumeric('time_estimated', $values, $errors) ?> <?= t('hours') ?><br/>

    <?= $this->formCheckbox('another_subtask', t('Create another sub-task'), 1, isset($values['another_subtask']) && $values['another_subtask'] == 1) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= $this->a(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
    </div>
</form>
