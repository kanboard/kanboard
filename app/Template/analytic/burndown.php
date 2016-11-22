<div class="page-header">
    <h2><?= t('Burndown chart') ?></h2>
</div>

<?php if (! $display_graph): ?>
    <p class="alert"><?= t('You need at least 2 days of data to show the chart.') ?></p>
<?php else: ?>
    <?= $this->app->component('chart-project-burndown', array(
        'metrics' => $metrics,
        'labelTotal' => t('Total for all columns'),
        'dateFormat' => e('%%Y-%%m-%%d'),
    )) ?>
<?php endif ?>

<hr/>

<form method="post" class="form-inline" action="<?= $this->url->href('AnalyticController', 'burndown', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <div class="form-inline-group">
        <?= $this->form->date(t('Start date'), 'from', $values) ?>
    </div>

    <div class="form-inline-group">
        <?= $this->form->date(t('End date'), 'to', $values) ?>
    </div>

    <div class="form-inline-group">
        <button type="submit" class="btn btn-blue"><?= t('Execute') ?></button>
    </div>
</form>

<p class="alert alert-info"><?= t('This chart show the task complexity over the time (Work Remaining).') ?></p>
