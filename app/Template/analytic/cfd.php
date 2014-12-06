<div class="page-header">
    <h2><?= t('Cumulative flow diagram') ?></h2>
</div>

<?php if (! $display_graph): ?>
    <p class="alert"><?= t('Not enough data to show the graph.') ?></p>
<?php else: ?>
    <section id="analytic-cfd">
        <div id="chart" data-url="<?= Helper\u('analytic', 'cfd', array('project_id' => $project['id'], 'from' => $values['from'], 'to' => $values['to'])) ?>"></div>
    </section>
<?php endif ?>

<hr/>

<form method="post" class="form-inline" action="<?= Helper\u('analytic', 'cfd', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>

    <div class="form-inline-group">
        <?= Helper\form_label(t('Start Date'), 'from') ?>
        <?= Helper\form_text('from', $values, array(), array('required', 'placeholder="'.Helper\in_list($date_format, $date_formats).'"'), 'form-date') ?>
    </div>

    <div class="form-inline-group">
        <?= Helper\form_label(t('End Date'), 'to') ?>
        <?= Helper\form_text('to', $values, array(), array('required', 'placeholder="'.Helper\in_list($date_format, $date_formats).'"'), 'form-date') ?>
    </div>

    <div class="form-inline-group">
        <input type="submit" value="<?= t('Execute') ?>" class="btn btn-blue"/>
    </div>
</form>
