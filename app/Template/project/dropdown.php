<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><strong>#<?= $project['id'] ?> <i class="fa fa-caret-down"></i></strong></a>
    <ul>
        <li>
            <?= $this->url->icon('th', t('Board'), 'BoardViewController', 'show', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= $this->url->icon('list', t('Listing'), 'TaskListController', 'show', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= $this->modal->medium('dashboard', t('Activity'), 'ActivityController', 'project', array('project_id' => $project['id'])) ?>
        </li>

        <?php if ($this->user->hasProjectAccess('AnalyticController', 'taskDistribution', $project['id'])): ?>
            <li>
                <?= $this->modal->large('line-chart', t('Analytics'), 'AnalyticController', 'taskDistribution', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>

        <?= $this->hook->render('template:project:dropdown', array('project' => $project)) ?>

        <?php if ($this->user->hasProjectAccess('ProjectEditController', 'show', $project['id'])): ?>
            <li>
                <?= $this->url->icon('cog', t('Configure this project'), 'ProjectViewController', 'show', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>
    </ul>
</div>
