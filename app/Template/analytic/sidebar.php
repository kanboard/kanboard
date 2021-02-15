<div class="sidebar">
    <ul>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'taskDistribution') ?>>
            <?= $this->modal->replaceLink(t('Task distribution'), 'AnalyticController', 'taskDistribution', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'userDistribution') ?>>
            <?= $this->modal->replaceLink(t('User repartition'), 'AnalyticController', 'userDistribution', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'cfd') ?>>
            <?= $this->modal->replaceLink(t('Cumulative flow diagram'), 'AnalyticController', 'cfd', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'burndown') ?>>
            <?= $this->modal->replaceLink(t('Burndown chart'), 'AnalyticController', 'burndown', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'averageTimeByColumn') ?>>
            <?= $this->modal->replaceLink(t('Average time into each column'), 'AnalyticController', 'averageTimeByColumn', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'leadAndCycleTime') ?>>
            <?= $this->modal->replaceLink(t('Lead and cycle time'), 'AnalyticController', 'leadAndCycleTime', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'timeComparison') ?>>
            <?= $this->modal->replaceLink(t('Estimated vs actual time'), 'AnalyticController', 'timeComparison', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'estimatedVsActualByColumn') ?>>
            <?= $this->modal->replaceLink(t('Estimated vs actual time per column'), 'AnalyticController', 'estimatedVsActualByColumn', array('project_id' => $project['id'])) ?>
        </li>

        <?= $this->hook->render('template:analytic:sidebar', array('project' => $project)) ?>
    </ul>
</div>
