<div class="sidebar">
    <h2><?= t('Reportings') ?></h2>
    <ul>
        <li>
            <?= Helper\a(t('Task distribution'), 'analytic', 'tasks', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= Helper\a(t('User repartition'), 'analytic', 'users', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= Helper\a(t('Cumulative flow diagram'), 'analytic', 'cfd', array('project_id' => $project['id'])) ?>
        </li>
    </ul>
</div>