<section id="main">
    <div class="page-header page-header-mobile">
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
            <li>
                <span class="dropdown">
                    <span>
                        <i class="fa fa-caret-down"></i> <a href="#" class="dropdown-menu"><?= t('Change dashboard view') ?></a>
                        <ul>
                            <li>
                                <a href="#" class="dashboard-toggle" data-toggle="projects"><?= t('Show/hide projects') ?></a>
                            </li>
                            <li>
                                <a href="#" class="dashboard-toggle" data-toggle="tasks"><?= t('Show/hide tasks') ?></a>
                            </li>
                            <li>
                                <a href="#" class="dashboard-toggle" data-toggle="subtasks"><?= t('Show/hide subtasks') ?></a>
                            </li>
                            <li>
                                <a href="#" class="dashboard-toggle" data-toggle="calendar"><?= t('Show/hide calendar') ?></a>
                            </li>
                            <li>
                                <a href="#" class="dashboard-toggle" data-toggle="activities"><?= t('Show/hide activities') ?></a>
                            </li>
                        </ul>
                    </span>
                </span>
            </li>
        </ul>
    </div>
    <section id="dashboard">
        <div class="dashboard-left-column">
            <div id="dashboard-projects"><?= $this->render('app/projects', array('paginator' => $project_paginator)) ?></div>
            <div id="dashboard-tasks"><?= $this->render('app/tasks', array('paginator' => $task_paginator)) ?></div>
            <div id="dashboard-subtasks"><?= $this->render('app/subtasks', array('paginator' => $subtask_paginator)) ?></div>
        </div>
        <div class="dashboard-right-column">
            <div id="dashboard-calendar">
                <div id="user-calendar"
                     data-check-url="<?= $this->u('calendar', 'user') ?>"
                     data-user-id="<?= $user_id ?>"
                     data-save-url="<?= $this->u('calendar', 'save') ?>"
                >
                </div>
            </div>
            <div id="dashboard-activities">
                <h2><?= t('Activity stream') ?></h2>
                <?= $this->render('event/events', array('events' => $events)) ?>
            </div>
        </div>
    </section>
</section>