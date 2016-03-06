<ul class="views">
    <li <?= $this->app->getRouterController() === 'ProjectOverview' ? 'class="active"' : '' ?>>
        <?= $this->url->link('<fa-eye> ' . t('Overview'), 'ProjectOverview', 'show', array('project_id' => $project['id']), false, 'view-overview', t('Keyboard shortcut: "%s"', 'v o')) ?>
    </li>
    <li <?= $this->app->getRouterController() === 'Board' ? 'class="active"' : '' ?>>
        <?= $this->url->link('<fa-th> ' . t('Board'), 'board', 'show', array('project_id' => $project['id'], 'search' => $filters['search']), false, 'view-board', t('Keyboard shortcut: "%s"', 'v b')) ?>
    </li>
    <li <?= $this->app->getRouterController() === 'Calendar' ? 'class="active"' : '' ?>>
        <?= $this->url->link('<fa-calendar> ' . t('Calendar'), 'calendar', 'show', array('project_id' => $project['id'], 'search' => $filters['search']), false, 'view-calendar', t('Keyboard shortcut: "%s"', 'v c')) ?>
    </li>
    <li <?= $this->app->getRouterController() === 'Listing' ? 'class="active"' : '' ?>>
        <?= $this->url->link('<fa-list> ' . t('List'), 'listing', 'show', array('project_id' => $project['id'], 'search' => $filters['search']), false, 'view-listing', t('Keyboard shortcut: "%s"', 'v l')) ?>
    </li>
    <?php if ($this->user->hasProjectAccess('gantt', 'project', $project['id'])): ?>
    <li <?= $this->app->getRouterController() === 'Gantt' ? 'class="active"' : '' ?>>
        <?= $this->url->link('<fa-sliders> ' . t('Gantt'), 'gantt', 'project', array('project_id' => $project['id'], 'search' => $filters['search']), false, 'view-gantt', t('Keyboard shortcut: "%s"', 'v g')) ?>
    </li>
    <?php endif ?>
</ul>
