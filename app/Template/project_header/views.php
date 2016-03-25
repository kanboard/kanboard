<ul class="views">
    <li <?= $this->app->getRouterController() === 'ProjectOverview' ? 'class="active"' : '' ?>>
        <i class="fa fa-eye fa-fw"></i>
        <?= $this->url->link(t('Overview'), 'ProjectOverview', 'show', array('project_id' => $project['id'], 'search' => $filters['search']), false, 'view-overview', t('Keyboard shortcut: "%s"', 'v o')) ?>
    </li>
    <li <?= $this->app->getRouterController() === 'Board' ? 'class="active"' : '' ?>>
        <i class="fa fa-th fa-fw"></i>
        <?= $this->url->link(t('Board'), 'board', 'show', array('project_id' => $project['id'], 'search' => $filters['search']), false, 'view-board', t('Keyboard shortcut: "%s"', 'v b')) ?>
    </li>
    <li <?= $this->app->getRouterController() === 'Calendar' ? 'class="active"' : '' ?>>
        <i class="fa fa-calendar fa-fw"></i>
        <?= $this->url->link(t('Calendar'), 'calendar', 'show', array('project_id' => $project['id'], 'search' => $filters['search']), false, 'view-calendar', t('Keyboard shortcut: "%s"', 'v c')) ?>
    </li>
    <li <?= $this->app->getRouterController() === 'Listing' ? 'class="active"' : '' ?>>
        <i class="fa fa-list fa-fw"></i>
        <?= $this->url->link(t('List'), 'listing', 'show', array('project_id' => $project['id'], 'search' => $filters['search']), false, 'view-listing', t('Keyboard shortcut: "%s"', 'v l')) ?>
    </li>
    <?php if ($this->user->hasProjectAccess('gantt', 'project', $project['id'])): ?>
    <li <?= $this->app->getRouterController() === 'Gantt' ? 'class="active"' : '' ?>>
        <i class="fa fa-sliders fa-fw"></i>
        <?= $this->url->link(t('Gantt'), 'gantt', 'project', array('project_id' => $project['id'], 'search' => $filters['search']), false, 'view-gantt', t('Keyboard shortcut: "%s"', 'v g')) ?>
    </li>
    <?php endif ?>
</ul>