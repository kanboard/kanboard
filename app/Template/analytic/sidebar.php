<div class="sidebar">
    <h2><?= t('Reportings') ?></h2>
    <ul>
        <li>
            <?= Helper\a(t('Task distribution'), 'analytic', 'repartition', array('project_id' => $project['id'])) ?>
        </li>
    </ul>
</div>