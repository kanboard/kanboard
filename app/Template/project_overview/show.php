<section id="main">
    <?= $this->projectHeader->render($project, 'ProjectOverviewController', 'show') ?>
    <?= $this->render('project_overview/columns', array('project' => $project, 'columns' => $columns)) ?>
    <?= $this->hook->render('template:project-overview:before-description', array('project' => $project)) ?>
    <?= $this->render('project_overview/description', array('project' => $project)) ?>
    <?= $this->render('project_overview/expanses', array('project' => $project, 'tasks' => $tasks, 'referenceCurrency' => $referenceCurrency)) ?>
    <?= $this->render('project_overview/attachments', array('project' => $project, 'images' => $images, 'files' => $files)) ?>
    <?= $this->render('project_overview/information', array('project' => $project, 'users' => $users, 'roles' => $roles)) ?>
    <?= $this->render('project_overview/activity', array('project' => $project, 'events' => $events)) ?>
</section>
