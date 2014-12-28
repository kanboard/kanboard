<div class="page-header">
    <h2><?= t('Task distribution') ?></h2>
</div>

<?php if (empty($metrics)): ?>
    <p class="alert"><?= t('Not enough data to show the graph.') ?></p>
<?php else: ?>
    <section id="analytic-task-repartition">

    <div id="chart" data-url="<?= $this->u('analytic', 'tasks', array('project_id' => $project['id'])) ?>"></div>

    <table>
        <tr>
            <th><?= t('Column') ?></th>
            <th><?= t('Number of tasks') ?></th>
            <th><?= t('Percentage') ?></th>
        </tr>
        <?php foreach ($metrics as $metric): ?>
        <tr>
            <td>
                <?= $this->e($metric['column_title']) ?>
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
