<div class="page-header">
    <h2><?= t('Task distribution') ?></h2>
</div>
<section id="analytic-repartition">

<div id="chart" data-url="<?= Helper\u('analytic', 'repartition', array('project_id' => $project['id'])) ?>"></div>

<table>
    <tr>
        <th><?= t('Column') ?></th>
        <th><?= t('Number of tasks') ?></th>
        <th><?= t('Percentage') ?></th>
    </tr>
    <?php foreach ($metrics as $metric): ?>
    <tr>
        <td>
            <?= Helper\escape($metric['column_title']) ?>
        </td>
        <td>
            <?= $metric['nb_tasks'] ?>
        </td>
        <td>
            <?= n($metric['percentage']) ?>%
        </td>
    </tr>
    <?php endforeach ?>
</table>

</section>
