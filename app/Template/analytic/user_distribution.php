<?php if (! $is_ajax): ?>
    <div class="page-header">
        <h2><?= t('User repartition') ?></h2>
    </div>
<?php endif ?>

<?php if (empty($metrics)): ?>
    <p class="alert"><?= t('Not enough data to show the graph.') ?></p>
<?php else: ?>
    <?= $this->app->component('chart-project-user-distribution', array(
        'metrics' => $metrics,
    )) ?>

    <table class="table-striped">
        <tr>
            <th><?= t('User') ?></th>
            <th><?= t('Number of tasks') ?></th>
            <th><?= t('Percentage') ?></th>
        </tr>
        <?php foreach ($metrics as $metric): ?>
        <tr>
            <td>
                <?= $this->text->e($metric['user']) ?>
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
<?php endif ?>
