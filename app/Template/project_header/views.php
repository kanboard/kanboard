<ul class="views">
    <li <?= $this->app->checkMenuSelection('ProjectOverviewController') ?>>
        <?= $this->url->link('<i class="fa fa-eye fa-fw"></i>' . t('Overview'), 'ProjectOverviewController', 'show', array('project_id' => $project['id'], 'search' => $filters['search']), false, 'view-overview', t('Keyboard shortcut: "%s"', 'v o')) ?>
    </li>
    <li <?= $this->app->checkMenuSelection('BoardViewController') ?>>
        <?= $this->url->link('<i class="fa fa-th fa-fw"></i>' . t('Board'), 'BoardViewController', 'show', array('project_id' => $project['id'], 'search' => $filters['search']), false, 'view-board', t('Keyboard shortcut: "%s"', 'v b')) ?>
    </li>
    <li <?= $this->app->checkMenuSelection('CalendarController') ?>>
        <?= $this->url->link('<i class="fa fa-calendar fa-fw"></i>' . t('Calendar'), 'CalendarController', 'show', array('project_id' => $project['id'], 'search' => $filters['search']), false, 'view-calendar', t('Keyboard shortcut: "%s"', 'v c')) ?>
    </li>
    <li <?= $this->app->checkMenuSelection('TaskListController') ?>>
        <?= $this->url->link('<i class="fa fa-list fa-fw"></i>' . t('List'), 'TaskListController', 'show', array('project_id' => $project['id'], 'search' => $filters['search']), false, 'view-listing', t('Keyboard shortcut: "%s"', 'v l')) ?>
    </li>
    <?php if ($this->user->hasProjectAccess('TaskGanttController', 'show', $project['id'])): ?>
    <li <?= $this->app->checkMenuSelection('TaskGanttController') ?>>
        <?= $this->url->link('<i class="fa fa-sliders fa-fw"></i>' . t('Gantt'), 'TaskGanttController', 'show', array('project_id' => $project['id'], 'search' => $filters['search']), false, 'view-gantt', t('Keyboard shortcut: "%s"', 'v g')) ?>
    </li>
    <?php endif ?>
</ul>
