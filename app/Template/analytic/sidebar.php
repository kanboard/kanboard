<div class="sidebar">
    <h2><?= t('Reportings') ?></h2>
    <ul>
        <li>
            <?= $this->url->link(t('Task distribution'), 'analytic', 'tasks', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= $this->url->link(t('User repartition'), 'analytic', 'users', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= $this->url->link(t('Cumulative flow diagram'), 'analytic', 'cfd', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= $this->url->link(t('Burndown chart'), 'analytic', 'burndown', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= $this->url->link(t('Average time into each column'), 'analytic', 'averageTimeByColumn', array('project_id' => $project['id'])) ?>
        </li>
    </ul>
</div>