<div class="search">
    <form method="get" action="<?= $this->url->dir() ?>" class="search">
        <?= $this->form->hidden('controller', array('controller' => 'search')) ?>
        <?= $this->form->hidden('action', array('action' => 'index')) ?>
        <?= $this->form->text('search', array(), array(), array('placeholder="'.t('Search').'"'), 'form-input-large') ?>
    </form>

    <?= $this->render('app/filters_helper') ?>
</div>

<?= $this->render('app/projects', array('paginator' => $project_paginator)) ?>
<?= $this->render('app/tasks', array('paginator' => $task_paginator)) ?>
<?= $this->render('app/subtasks', array('paginator' => $subtask_paginator)) ?>