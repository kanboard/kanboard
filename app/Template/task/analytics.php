<div class="page-header">
    <h2><?= t('Analytics') ?></h2>
</div>

<div class="listing">
    <ul>
        <li><?= t('Lead time: ').'<strong>'.$this->dt->duration($lead_time) ?></strong></li>
        <li><?= t('Cycle time: ').'<strong>'.$this->dt->duration($cycle_time) ?></strong></li>
    </ul>
</div>

<h3 id="analytic-task-time-column"><?= t('Time spent into each column') ?></h3>
<div id="chart" data-metrics='<?= json_encode($time_spent_columns, JSON_HEX_APOS) ?>' data-label="<?= t('Time spent') ?>"></div>
<table class="table-stripped">
    <tr>
        <th><?= t('Column') ?></th>
        <th><?= t('Time spent') ?></th>
    </tr>
    <?php foreach ($time_spent_columns as $column): ?>
    <tr>
        <td><?= $this->e($column['title']) ?></td>
        <td><?= $this->dt->duration($column['time_spent']) ?></td>
    </tr>
    <?php endforeach ?>
</table>

<div class="alert alert-info">
    <ul>
        <li><?= t('The lead time is the duration between the task creation and the completion.') ?></li>
        <li><?= t('The cycle time is the duration between the start date and the completion.') ?></li>
        <li><?= t('If the task is not closed the current time is used instead of the completion date.') ?></li>
    </ul>
</div>

<?= $this->asset->js('assets/js/vendor/d3.v3.min.js') ?>
<?= $this->asset->js('assets/js/vendor/c3.min.js') ?>