<div class="page-header">
    <h2><?= t('Add a sub-task') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('subtask', 'save', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('task_id', $values) ?>

    <?= $this->form->label(t('Title'), 'title') ?>
    <?= $this->form->text('title', $values, $errors, array('required', 'autofocus', 'maxlength="255"')) ?><br/>

    <?= $this->form->label(t('Assignee'), 'user_id') ?>
    <?= $this->form->select('user_id', $users_list, $values, $errors) ?><br/>

    <?= $this->form->label(t('Original estimate'), 'time_estimated') ?>
    <?= $this->form->numeric('time_estimated', $values, $errors) ?> <?= t('hours') ?><br/>

    <?= $this->form->checkbox('another_subtask', t('Create another sub-task'), 1, isset($values['another_subtask']) && $values['another_subtask'] == 1) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
    </div>
</form>
