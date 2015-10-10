<div class="page-header">
    <h2><?= t('Average time spent into each column') ?></h2>
</div>

<?php if (empty($metrics)): ?>
    <p class="alert"><?= t('Not enough data to show the graph.') ?></p>
<?php else: ?>
    <section id="analytic-avg-time-column">

        <div id="chart" data-metrics='<?= json_encode($metrics, JSON_HEX_APOS) ?>' data-label="<?= t('Average time spent') ?>"></div>

        <table class="table-stripped">
        <tr>
            <th><?= t('Column') ?></th>
            <th><?= t('Average time spent') ?></th>
        </tr>
        <?php foreach ($metrics as $column): ?>
        <tr>
            <td><?= $this->e($column['title']) ?></td>
            <td><?= $this->dt->duration($column['average']) ?></td>
        </tr>
        <?php endforeach ?>
        </table>

        <p class="alert alert-info">
            <?= t('This chart show the average time spent into each column for the last %d tasks.', 1000) ?>
        </p>
    </section>
<?php endif ?>
