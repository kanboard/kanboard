<form method="get" action="?" autocomplete="off">
    <?= $this->form->hidden('controller', array('controller' => 'search')) ?>
    <?= $this->form->hidden('action', array('controller' => 'index')) ?>
    <?= $this->form->text('search', array(), array(), array('placeholder="'.t('Search').'"'), 'form-input-large') ?>
    <input type="submit" value="<?= t('Search') ?>" class="btn btn-blue"/>
</form>

<?= $this->render('app/projects', array('paginator' => $project_paginator)) ?>
<?= $this->render('app/tasks', array('paginator' => $task_paginator)) ?>
<?= $this->render('app/subtasks', array('paginator' => $subtask_paginator)) ?>