<div class="sidebar">
    <h2><?= t('Reportings') ?></h2>
    <ul>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'tasks') ?>>
            <?= $this->url->link(t('Task distribution'), 'AnalyticController', 'tasks', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'users') ?>>
            <?= $this->url->link(t('User repartition'), 'AnalyticController', 'users', array('project_id' => $project['id'])) ?>
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
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'compareHours') ?>>
            <?= $this->url->link(t('Estimated vs actual time'), 'AnalyticController', 'compareHours', array('project_id' => $project['id'])) ?>
        </li>

        <?= $this->hook->render('template:analytic:sidebar', array('project' => $project)) ?>

    </ul>
</div>
