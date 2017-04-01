<div class="page-header">
    <ul>
        <?= $this->hook->render('template:project-list:menu:before') ?>

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

        <?php if ($this->user->hasAccess('ProjectUserOverviewController', 'managers')): ?>
            <li><?= $this->url->icon('user', t('Users overview'), 'ProjectUserOverviewController', 'managers') ?></li>
        <?php endif ?>

        <?= $this->hook->render('template:project-list:menu:after') ?>
    </ul>
</div>
<?php if ($paginator->isEmpty()): ?>
    <p class="alert"><?= t('There is no project.') ?></p>
<?php else: ?>
    <div class="table-list">
        <?= $this->render('project_list/header', array('paginator' => $paginator)) ?>
        <?php foreach ($paginator->getCollection() as $project): ?>
            <div class="table-list-row table-border-left">
                <?= $this->render('project_list/project_title', array(
                    'project' => $project,
                )) ?>

                <?= $this->render('project_list/project_details', array(
                    'project' => $project,
                )) ?>

                <?= $this->render('project_list/project_icons', array(
                    'project' => $project,
                )) ?>
            </div>
        <?php endforeach ?>
    </div>

    <?= $paginator ?>
<?php endif ?>
