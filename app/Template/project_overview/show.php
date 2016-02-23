<section id="main">
    <?= $this->render('project_header/header', array(
        'project' => $project,
        'filters' => $filters,
    )) ?>

    <?= $this->render('project_overview/columns', array('project' => $project)) ?>
    <?= $this->render('project_overview/description', array('project' => $project)) ?>
    <?= $this->render('project_overview/files', array('project' => $project, 'images' => $images, 'files' => $files)) ?>
    <?= $this->render('project_overview/information', array('project' => $project, 'users' => $users, 'roles' => $roles)) ?>

    <div class="page-header">
        <h2><?= t('Last activity') ?></h2>
    </div>
    <?= $this->render('event/events', array('events' => $events)) ?>
</section>
