<div class="sidebar">
    <h2><?= t('Reportings') ?></h2>
    <ul>
        <li <?= $this->app->checkMenuSelection('analytic', 'tasks') ?>>
            <?= $this->url->link(t('Task distribution'), 'analytic', 'tasks', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('analytic', 'users') ?>>
            <?= $this->url->link(t('User repartition'), 'analytic', 'users', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('analytic', 'cfd') ?>>
            <?= $this->url->link(t('Cumulative flow diagram'), 'analytic', 'cfd', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('analytic', 'burndown') ?>>
            <?= $this->url->link(t('Burndown chart'), 'analytic', 'burndown', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('analytic', 'averageTimeByColumn') ?>>
            <?= $this->url->link(t('Average time into each column'), 'analytic', 'averageTimeByColumn', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('analytic', 'leadAndCycleTime') ?>>
            <?= $this->url->link(t('Lead and cycle time'), 'analytic', 'leadAndCycleTime', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('analytic', 'compareHours') ?>>
            <?= $this->url->link(t('Estimated vs actual time'), 'analytic', 'compareHours', array('project_id' => $project['id'])) ?>
        </li>
        
        <?= $this->hook->render('template:analytic:sidebar', array('project' => $project)) ?>
        
    </ul>
</div>
