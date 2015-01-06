<div class="page-header">
    <h2><?= t('Edit a sub-task') ?></h2>
</div>

<form method="post" action="<?= $this->u('subtask', 'update', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id'])) ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

    <?= $this->formHidden('id', $values) ?>
    <?= $this->formHidden('task_id', $values) ?>

    <?= $this->formLabel(t('Title'), 'title') ?>
    <?= $this->formText('title', $values, $errors, array('required autofocus')) ?><br/>

    <?= $this->formLabel(t('Status'), 'status') ?>
    <?= $this->formSelect('status', $status_list, $values, $errors) ?><br/>

    <?= $this->formLabel(t('Assignee'), 'user_id') ?>
    <?= $this->formSelect('user_id', $users_list, $values, $errors) ?><br/>

    <?= $this->formLabel(t('Original estimate'), 'time_estimated') ?>
    <?= $this->formNumeric('time_estimated', $values, $errors) ?> <?= t('hours') ?><br/>

    <?= $this->formLabel(t('Time spent'), 'time_spent') ?>
    <?= $this->formNumeric('time_spent', $values, $errors) ?> <?= t('hours') ?><br/>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= $this->a(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
    </div>
</form>
