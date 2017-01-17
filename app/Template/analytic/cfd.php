<?php if (! $is_ajax): ?>
    <div class="page-header">
        <h2><?= t('Cumulative flow diagram') ?></h2>
    </div>
<?php endif ?>

<?php if (! $display_graph): ?>
    <p class="alert"><?= t('You need at least 2 days of data to show the chart.') ?></p>
<?php else: ?>
    <?= $this->app->component('chart-project-cumulative-flow', array(
        'metrics' => $metrics,
        'dateFormat' => e('%%Y-%%m-%%d'),
    )) ?>
<?php endif ?>

<hr/>

<form method="post" class="form-inline" action="<?= $this->url->href('AnalyticController', 'cfd', array('project_id' => $project['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->date(t('Start date'), 'from', $values) ?>
    <?= $this->form->date(t('End date'), 'to', $values) ?>
    <?= $this->modal->submitButtons(array('submitLabel' => t('Execute'))) ?>
</form>
