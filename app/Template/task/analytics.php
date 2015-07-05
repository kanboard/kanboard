<div class="page-header">
    <h2><?= t('Analytics') ?></h2>
</div>

<div class="listing">
    <ul>
        <li><?= t('Lead time: ').'<strong>'.$this->dt->duration($lead_time) ?></strong></li>
        <li><?= t('Cycle time: ').'<strong>'.$this->dt->duration($cycle_time) ?></strong></li>
    </ul>
</div>

<h3><?= t('Average time spent for each column') ?></h3>
<table class="table-stripped">
    <tr>
        <th><?= t('Column') ?></th>
        <th><?= t('Average time spent') ?></th>
    </tr>
    <?php foreach ($column_averages as $column): ?>
    <tr>
        <td><?= $this->e($column['title']) ?></td>
        <td><?= $this->dt->duration($column['time_spent']) ?></td>
    </tr>
    <?php endforeach ?>
</table>

<div class="alert alert-info">
    <ul>
        <li><?= t('The lead time is the time between the task creation and the completion.') ?></li>
        <li><?= t('The cycle time is the time between the start date and the completion.') ?></li>
        <li><?= t('If the task is not closed the current time is used.') ?></li>
    </ul>
</div>