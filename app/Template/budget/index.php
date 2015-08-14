<div class="page-header">
    <h2><?= t('Budget overview') ?></h2>
</div>

<?php if (! empty($daily_budget)): ?>
<div id="budget-chart">
    <div id="chart"
         data-date-format="<?= e('%%Y-%%m-%%d') ?>"
         data-metrics='<?= json_encode($daily_budget, JSON_HEX_APOS) ?>'
         data-labels='<?= json_encode(array('in' => t('Budget line'), 'out' => t('Expenses'), 'left' => t('Remaining'), 'value' => t('Amount'), 'date' => t('Date'), 'type' => t('Type')), JSON_HEX_APOS) ?>'></div>
</div>
<hr/>
<table class="table-fixed table-stripped">
    <tr>
        <th><?= t('Date') ?></td>
        <th><?= t('Budget line') ?></td>
        <th><?= t('Expenses') ?></td>
        <th><?= t('Remaining') ?></td>
    </tr>
    <?php foreach ($daily_budget as $line): ?>
    <tr>
        <td><?= dt('%B %e, %Y', strtotime($line['date'])) ?></td>
        <td><?= n($line['in']) ?></td>
        <td><?= n($line['out']) ?></td>
        <td><?= n($line['left']) ?></td>
    </tr>
    <?php endforeach ?>
</table>
<?php else: ?>
    <p class="alert"><?= t('There is not enough data to show something.') ?></p>
<?php endif ?>

<?= $this->asset->js('assets/js/vendor/d3.v3.min.js') ?>
<?= $this->asset->js('assets/js/vendor/c3.min.js') ?>