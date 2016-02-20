<div class="page-header">
    <h2><?= t('Sub-Tasks') ?></h2>
</div>

<div id="subtasks">

    <?= $this->render('subtask/table', array('subtasks' => $subtasks, 'task' => $task, 'editable' => $editable)) ?>

    <?php if ($editable && $this->user->hasProjectAccess('subtask', 'save', $task['project_id'])): ?>
        <form method="post" action="<?= $this->url->href('subtask', 'save', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">
            <?= $this->form->csrf() ?>
            <?= $this->form->hidden('task_id', array('task_id' => $task['id'])) ?>
            <?= $this->form->text('title', array(), array(), array('required', 'placeholder="'.t('Type here to create a new sub-task').'"')) ?>
            <?= $this->form->numeric('time_estimated', array(), array(), array('placeholder="'.t('Original estimate').'"')) ?>
            <?= $this->form->select('user_id', $users_list, array(), array(), array('placeholder="'.t('Assignee').'"')) ?>
            <input type="submit" value="<?= t('Add') ?>" class="btn btn-blue"/>
        </form>
    <?php endif ?>

</div>
