<div class="filter-box">
    <form method="get" action="<?= $this->url->dir() ?>" class="search">
        <?= $this->form->hidden('controller', array('controller' => 'SearchController')) ?>
        <?= $this->form->hidden('action', array('action' => 'index')) ?>

        <div class="input-addon">
            <?= $this->form->text('search', array(), array(), array('placeholder="'.t('Search').'"'), 'input-addon-field') ?>
            <div class="input-addon-item">
                <?= $this->render('app/filters_helper') ?>
            </div>
        </div>
    </form>
</div>

<?= $this->render('dashboard/projects', array('paginator' => $project_paginator, 'user' => $user)) ?>
<?= $this->render('dashboard/tasks', array('paginator' => $task_paginator, 'user' => $user)) ?>
<?= $this->render('dashboard/subtasks', array('paginator' => $subtask_paginator, 'user' => $user)) ?>

<?= $this->hook->render('template:dashboard:show', array('user' => $user)) ?>
