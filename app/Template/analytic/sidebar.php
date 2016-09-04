<div class="sidebar">
    <ul>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'taskDistribution') ?>>
            <?= $this->url->link(t('Task distribution'), 'AnalyticController', 'taskDistribution', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'userDistribution') ?>>
            <?= $this->url->link(t('User repartition'), 'AnalyticController', 'userDistribution', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'cfd') ?>>
            <?= $this->url->link(t('Cumulative flow diagram'), 'AnalyticController', 'cfd', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'burndown') ?>>
            <?= $this->url->link(t('Burndown chart'), 'AnalyticController', 'burndown', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'averageTimeByColumn') ?>>
            <?= $this->url->link(t('Average time into each column'), 'AnalyticController', 'averageTimeByColumn', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'leadAndCycleTime') ?>>
            <?= $this->url->link(t('Lead and cycle time'), 'AnalyticController', 'leadAndCycleTime', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'timeComparison') ?>>
            <?= $this->url->link(t('Estimated vs actual time'), 'AnalyticController', 'timeComparison', array('project_id' => $project['id'])) ?>
        </li>

        <?= $this->hook->render('template:analytic:sidebar', array('project' => $project)) ?>

    </ul>
</div>
