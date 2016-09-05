<div class="page-header">
    <h2><?= t('Average Lead and Cycle time') ?></h2>
</div>

<div class="listing">
    <ul>
        <li><?= t('Average lead time: ').'<strong>'.$this->dt->duration($average['avg_lead_time']) ?></strong></li>
        <li><?= t('Average cycle time: ').'<strong>'.$this->dt->duration($average['avg_cycle_time']) ?></strong></li>
    </ul>
</div>

<?php if (empty($metrics)): ?>
    <p class="alert"><?= t('Not enough data to show the graph.') ?></p>
<?php else: ?>
    <section id="analytic-lead-cycle-time">

        <div id="chart" data-metrics='<?= json_encode($metrics, JSON_HEX_APOS) ?>' data-label-cycle="<?= t('Cycle Time') ?>" data-label-lead="<?= t('Lead Time') ?>"></div>

        <form method="post" class="form-inline" action="<?= $this->url->href('AnalyticController', 'leadAndCycleTime', array('project_id' => $project['id'])) ?>" autocomplete="off">

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

        <p class="alert alert-info">
            <?= t('This chart show the average lead and cycle time for the last %d tasks over the time.', 1000) ?>
        </p>
    </section>
<?php endif ?>
