<div class="dropdown">
    <a href="#" class="dropdown-menu dashboard-table-link">#<?= $project['id'] ?></a>
    <ul>
        <li>
            <?= $this->url->link('<i class="fa fa-th fa-fw"></i>' . t('Board'), 'BoardViewController', 'show', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= $this->url->link('<i class="fa fa-calendar fa-fw"></i>' . t('Calendar'), 'CalendarController', 'show', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= $this->url->link('<i class="fa fa-list fa-fw"></i>' . t('Listing'), 'TaskListController', 'show', array('project_id' => $project['id'])) ?>
        </li>
        <?php if ($this->user->hasProjectAccess('TaskGanttController', 'show', $project['id'])): ?>
        <li>
            <?= $this->url->link('<i class="fa fa-sliders fa-fw"></i>' . t('Gantt'), 'TaskGanttController', 'show', array('project_id' => $project['id'])) ?>
        </li>
        <?php endif ?>

        <li>
            <?= $this->url->link('<i class="fa fa-dashboard fa-fw"></i>&nbsp;' . t('Activity'), 'ActivityController', 'project', array('project_id' => $project['id'])) ?>
        </li>

        <?php if ($this->user->hasProjectAccess('AnalyticController', 'taskDistribution', $project['id'])): ?>
            <li>
                <?= $this->url->link('<i class="fa fa-line-chart fa-fw"></i>&nbsp;' . t('Analytics'), 'AnalyticController', 'taskDistribution', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>

        <?= $this->hook->render('template:project:dropdown', array('project' => $project)) ?>

        <?php if ($this->user->hasProjectAccess('ProjectEditController', 'edit', $project['id'])): ?>
            <li>
                <?= $this->url->link('<i class="fa fa-cog fa-fw"></i>' . t('Settings'), 'ProjectViewController', 'show', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>
    </ul>
</div>
