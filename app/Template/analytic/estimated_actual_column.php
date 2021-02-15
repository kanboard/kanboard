<?php if (! $is_ajax): ?>
    <div class="page-header">
        <h2><?= t('Estimated vs actual time per column') ?></h2>
    </div>
<?php endif ?>

<?php if (empty($metrics)): ?>
    <p class="alert"><?= t('Not enough data to show the graph.') ?></p>
<?php else: ?>
    <?= $this->app->component('chart-project-estimated-actual-column', array(
        'metrics' => $metrics,
        'labelSpent' => t('Hours Spent'),
        'labelEstimated' => t('Hours Estimated'),
    )) ?>

    <table class="table-striped">
        <tr>
            <th><?= t('Column') ?></th>
            <th><?= t('Hours Spent') ?></th>
            <th><?= t('Hours Estimated') ?></th>
        </tr>
        <?php foreach ($metrics as $column): ?>
            <tr>
                <td><?= $this->text->e($column['title']) ?></td>
                <td><?= $this->dt->durationHours($column['hours_spent']) ?></td>
                <td><?= $this->dt->durationHours($column['hours_estimated']) ?></td>
            </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>