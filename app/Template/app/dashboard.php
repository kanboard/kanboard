<section id="main">
    <div class="page-header">
        <ul>
            <?php if (Helper\is_admin()): ?>
                <li><i class="fa fa-plus fa-fw"></i><?= Helper\a(t('New project'), 'project', 'create') ?></li>
            <?php endif ?>
            <li><i class="fa fa-lock fa-fw"></i><?= Helper\a(t('New private project'), 'project', 'create', array('private' => 1)) ?></li>
            <li><i class="fa fa-folder fa-fw"></i><?= Helper\a(t('Project management'), 'project', 'index') ?></li>
            <?php if (Helper\is_admin()): ?>
                <li><i class="fa fa-user fa-fw"></i><?= Helper\a(t('User management'), 'user', 'index') ?></li>
                <li><i class="fa fa-cog fa-fw"></i><?= Helper\a(t('Settings'), 'config', 'index') ?></li>
            <?php endif ?>
        </ul>
    </div>
    <section id="dashboard">
        <div class="dashboard-left-column">
            <?= Helper\Template('app/projects', array('projects' => $projects, 'pagination' => $project_pagination)) ?>
            <?= Helper\Template('app/tasks', array('tasks' => $tasks, 'pagination' => $task_pagination)) ?>
            <?= Helper\Template('app/subtasks', array('subtasks' => $subtasks, 'pagination' => $subtask_pagination)) ?>
        </div>
        <div class="dashboard-right-column">
            <h2><?= t('Activity stream') ?></h2>
            <?= Helper\template('project/events', array('events' => $events)) ?>
        </div>
    </section>
</section>