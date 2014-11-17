<div class="page-header">
    <h2>
        <?= t('Daily project summary export for "%s"', $project['name']) ?>
    </h2>
</div>

<p class="alert alert-info"><?= t('This export contains the number of tasks per column grouped per day.') ?></p>

<form method="get" action="?" autocomplete="off">

    <?= Helper\form_hidden('controller', $values) ?>
    <?= Helper\form_hidden('action', $values) ?>
    <?= Helper\form_hidden('project_id', $values) ?>

    <?= Helper\form_label(t('Start Date'), 'from') ?>
    <?= Helper\form_text('from', $values, $errors, array('required', 'placeholder="'.Helper\in_list($date_format, $date_formats).'"'), 'form-date') ?><br/>

    <?= Helper\form_label(t('End Date'), 'to') ?>
    <?= Helper\form_text('to', $values, $errors, array('required', 'placeholder="'.Helper\in_list($date_format, $date_formats).'"'), 'form-date') ?>

    <div class="form-help"><?= t('Others formats accepted: %s and %s', date('Y-m-d'), date('Y_m_d')) ?></div>

    <div class="form-actions">
        <input type="submit" value="<?= t('Execute') ?>" class="btn btn-blue"/>
    </div>
</form>