<div class="page-header">
    <h2><?= t('User repartition') ?></h2>
</div>

<?php if (empty($metrics)): ?>
    <p class="alert"><?= t('Not enough data to show the graph.') ?></p>
<?php else: ?>
    <section id="analytic-user-repartition">

    <div id="chart" data-url="<?= Helper\u('analytic', 'users', array('project_id' => $project['id'])) ?>"></div>

    <table>
        <tr>
            <th><?= t('User') ?></th>
            <th><?= t('Number of tasks') ?></th>
            <th><?= t('Percentage') ?></th>
        </tr>
        <?php foreach ($metrics as $metric): ?>
        <tr>
            <td>
                <?= Helper\escape($metric['user']) ?>
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
<?php endif ?>
