<ul class="views">
    <li <?= $filters['controller'] === 'board' ? 'class="active"' : '' ?>>
        <i class="fa fa-th fa-fw"></i>
        <?= $this->url->link(t('Board'), 'board', 'show', array('project_id' => $project['id'], 'search' => $filters['search']), false, 'view-board', t('Keyboard shortcut: "%s"', 'v b')) ?>
    </li>
    <li <?= $filters['controller'] === 'calendar' ? 'class="active"' : '' ?>>
        <i class="fa fa-calendar fa-fw"></i>
        <?= $this->url->link(t('Calendar'), 'calendar', 'show', array('project_id' => $project['id'], 'search' => $filters['search']), false, 'view-calendar', t('Keyboard shortcut: "%s"', 'v c')) ?>
    </li>
    <li <?= $filters['controller'] === 'listing' ? 'class="active"' : '' ?>>
        <i class="fa fa-list fa-fw"></i>
        <?= $this->url->link(t('List'), 'listing', 'show', array('project_id' => $project['id'], 'search' => $filters['search']), false, 'view-listing', t('Keyboard shortcut: "%s"', 'v l')) ?>
    </li>
    <?php if ($this->user->hasProjectAccess('gantt', 'project', $project['id'])): ?>
    <li <?= $filters['controller'] === 'gantt' ? 'class="active"' : '' ?>>
        <i class="fa fa-sliders fa-fw"></i>
        <?= $this->url->link(t('Gantt'), 'gantt', 'project', array('project_id' => $project['id'], 'search' => $filters['search']), false, 'view-gantt', t('Keyboard shortcut: "%s"', 'v g')) ?>
    </li>
    <?php endif ?>
</ul>