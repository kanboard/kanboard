<div class="dropdown">
    <a href="#" class="dropdown-menu dashboard-table-link">#<?= $project['id'] ?></a>
    <ul>
        <li>
            <i class="fa fa-th fa-fw"></i>
            <?= $this->url->link(t('Board'), 'BoardViewController', 'show', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <i class="fa fa-calendar fa-fw"></i>
            <?= $this->url->link(t('Calendar'), 'CalendarController', 'show', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <i class="fa fa-list fa-fw"></i>
            <?= $this->url->link(t('Listing'), 'TaskListController', 'show', array('project_id' => $project['id'])) ?>
        </li>
        <?php if ($this->user->hasProjectAccess('TaskGanttController', 'show', $project['id'])): ?>
        <li>
            <i class="fa fa-sliders fa-fw"></i>
            <?= $this->url->link(t('Gantt'), 'TaskGanttController', 'show', array('project_id' => $project['id'])) ?>
        </li>
        <?php endif ?>

        <li>
            <i class="fa fa-dashboard fa-fw"></i>&nbsp;
            <?= $this->url->link(t('Activity'), 'ActivityController', 'project', array('project_id' => $project['id'])) ?>
        </li>

        <?php if ($this->user->hasProjectAccess('AnalyticController', 'taskDistribution', $project['id'])): ?>
            <li>
                <i class="fa fa-line-chart fa-fw"></i>&nbsp;
                <?= $this->url->link(t('Analytics'), 'AnalyticController', 'taskDistribution', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>

        <?= $this->hook->render('template:project:dropdown', array('project' => $project)) ?>

        <?php if ($this->user->hasProjectAccess('ProjectEditController', 'edit', $project['id'])): ?>
            <li>
                <i class="fa fa-cog fa-fw"></i>
                <?= $this->url->link(t('Settings'), 'ProjectViewController', 'show', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>
    </ul>
</div>
