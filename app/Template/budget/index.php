<?= $this->js('assets/js/vendor/d3.v3.4.8.min.js') ?>
<?= $this->js('assets/js/vendor/dimple.v2.1.2.min.js') ?>

<div class="page-header">
    <h2><?= t('Budget') ?></h2>
    <ul>
        <li><?= $this->a(t('Budget lines'), 'budget', 'create', array('project_id' => $project['id'])) ?></li>
        <li><?= $this->a(t('Cost breakdown'), 'budget', 'breakdown', array('project_id' => $project['id'])) ?></li>
    </ul>
</div>

<?php if (! empty($daily_budget)): ?>
<div id="budget-chart">
    <div id="chart"
         data-serie='<?= json_encode($daily_budget) ?>'
         data-labels='<?= json_encode(array('in' => t('Budget line'), 'out' => t('Expenses'), 'left' => t('Remaining'), 'value' => t('Amount'), 'date' => t('Date'), 'type' => t('Type'))) ?>'></div>
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
<?php endif ?>
