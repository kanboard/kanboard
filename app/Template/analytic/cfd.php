<div class="page-header">
    <h2><?= t('Cumulative flow diagram') ?></h2>
</div>

<?php if (! $display_graph): ?>
    <p class="alert"><?= t('Not enough data to show the graph.') ?></p>
<?php else: ?>
    <section id="analytic-cfd">
        <div id="chart" data-url="<?= $this->u('analytic', 'cfd', array('project_id' => $project['id'], 'from' => $values['from'], 'to' => $values['to'])) ?>"></div>
    </section>
<?php endif ?>

<hr/>

<form method="post" class="form-inline" action="<?= $this->u('analytic', 'cfd', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

    <div class="form-inline-group">
        <?= $this->formLabel(t('Start Date'), 'from') ?>
        <?= $this->formText('from', $values, array(), array('required', 'placeholder="'.$this->inList($date_format, $date_formats).'"'), 'form-date') ?>
    </div>

    <div class="form-inline-group">
        <?= $this->formLabel(t('End Date'), 'to') ?>
        <?= $this->formText('to', $values, array(), array('required', 'placeholder="'.$this->inList($date_format, $date_formats).'"'), 'form-date') ?>
    </div>

    <div class="form-inline-group">
        <input type="submit" value="<?= t('Execute') ?>" class="btn btn-blue"/>
    </div>
</form>
