<?= $this->render('task/details', array(
    'task' => $task,
    'tags' => $tags,
    'project' => $project,
    'editable' => false,
)) ?>

<div class="page-header">
    <h2><?= t('Analytics') ?></h2>
</div>

<div class="panel">
    <ul>
        <li><?= t('Lead time: ').'<strong>'.$this->dt->duration($lead_time) ?></strong></li>
        <li><?= t('Cycle time: ').'<strong>'.$this->dt->duration($cycle_time) ?></strong></li>
    </ul>
</div>

<h3><?= t('Time spent into each column') ?></h3>

<?= $this->app->component('chart-task-time-column', array(
    'metrics' => $time_spent_columns,
    'label' => t('Time spent'),
)) ?>

<table class="table-striped">
    <tr>
        <th><?= t('Column') ?></th>
        <th><?= t('Time spent') ?></th>
    </tr>
    <?php foreach ($time_spent_columns as $column): ?>
    <tr>
        <td><?= $this->text->e($column['title']) ?></td>
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
