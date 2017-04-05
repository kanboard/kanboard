<div class="page-header">
    <ul>
        <?php if ($this->user->hasAccess('ProjectCreationController', 'create')): ?>
            <li>
                <?= $this->modal->medium('plus', t('New project'), 'ProjectCreationController', 'create') ?>
            </li>
        <?php endif ?>
        <?php if ($this->app->config('disable_private_project', 0) == 0): ?>
            <li>
                <?= $this->modal->medium('lock', t('New private project'), 'ProjectCreationController', 'createPrivate') ?>
            </li>
        <?php endif ?>
        <li>
            <?= $this->url->icon('folder', t('Project management'), 'ProjectListController', 'show') ?>
        </li>
        <li>
            <?= $this->modal->medium('dashboard', t('My activity stream'), 'ActivityController', 'user') ?>
        </li>
        <?= $this->hook->render('template:dashboard:page-header:menu', array('user' => $user)) ?>
    </ul>
</div>

<div class="filter-box margin-bottom">
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

<?php if (empty($results)): ?>
    <p class="alert"><?= t('There is nothing assigned to you.') ?></p>
<?php else: ?>
    <?php foreach ($results as $result): ?>
        <?php if (! $result['paginator']->isEmpty()): ?>
            <div class="page-header">
                <h2><?= $this->url->link($this->text->e($result['project_name']), 'BoardViewController', 'show', array('project_id' => $result['project_id'])) ?></h2>
            </div>

            <div class="table-list">
                <?= $this->render('task_list/header', array(
                    'paginator' => $result['paginator'],
                )) ?>

                <?php foreach ($result['paginator']->getCollection() as $task): ?>
                    <div class="table-list-row color-<?= $task['color_id'] ?>">
                        <?= $this->render('task_list/task_title', array(
                            'task' => $task,
                        )) ?>

                        <?= $this->render('task_list/task_details', array(
                            'task' => $task,
                        )) ?>

                        <?= $this->render('task_list/task_avatars', array(
                            'task' => $task,
                        )) ?>

                        <?= $this->render('task_list/task_icons', array(
                            'task' => $task,
                        )) ?>

                        <?= $this->render('task_list/task_subtasks', array(
                            'task'    => $task,
                            'user_id' => $user['id'],
                        )) ?>
                    </div>
                <?php endforeach ?>
            </div>

            <?= $result['paginator'] ?>
        <?php endif ?>
    <?php endforeach ?>
<?php endif ?>

<?= $this->hook->render('template:dashboard:show', array('user' => $user)) ?>
