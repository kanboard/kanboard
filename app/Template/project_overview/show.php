<section id="main">
    <?= $this->render('project_header/header', array(
        'project' => $project,
        'filters' => $filters,
    )) ?>

    <?= $this->render('project_overview/columns', array('project' => $project)) ?>
    <?= $this->render('project_overview/description', array('project' => $project)) ?>
    <?= $this->render('project_overview/attachments', array('project' => $project, 'images' => $images, 'files' => $files)) ?>
    <?= $this->render('project_overview/information', array('project' => $project, 'users' => $users, 'roles' => $roles)) ?>
    <?= $this->render('project_overview/activity', array('project' => $project, 'events' => $events)) ?>
</section>
