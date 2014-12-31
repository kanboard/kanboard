<section id="main">
    <div class="page-header">
        <ul>
            <?php if ($this->userSession->isAdmin()): ?>
                <li><i class="fa fa-plus fa-fw"></i><?= $this->a(t('New project'), 'project', 'create') ?></li>
            <?php endif ?>
            <li><i class="fa fa-lock fa-fw"></i><?= $this->a(t('New private project'), 'project', 'create', array('private' => 1)) ?></li>
            <li><i class="fa fa-folder fa-fw"></i><?= $this->a(t('Project management'), 'project', 'index') ?></li>
            <?php if ($this->userSession->isAdmin()): ?>
                <li><i class="fa fa-user fa-fw"></i><?= $this->a(t('User management'), 'user', 'index') ?></li>
                <li><i class="fa fa-cog fa-fw"></i><?= $this->a(t('Settings'), 'config', 'index') ?></li>
            <?php endif ?>
        </ul>
    </div>
    <section id="dashboard">
        <div class="dashboard-left-column">
            <?= $this->render('app/projects', array('projects' => $projects, 'pagination' => $project_pagination)) ?>
            <?= $this->render('app/tasks', array('tasks' => $tasks, 'pagination' => $task_pagination)) ?>
            <?= $this->render('app/subtasks', array('subtasks' => $subtasks, 'pagination' => $subtask_pagination)) ?>
        </div>
        <div class="dashboard-right-column">
            <h2><?= t('Activity stream') ?></h2>
            <?= $this->render('project/events', array('events' => $events)) ?>
        </div>
    </section>
</section>