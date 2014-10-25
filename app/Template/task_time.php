<form method="post" action="<?= Helper\u('task', 'time', array('task_id' => $values['id'])) ?>" class="form-inline task-time-form" autocomplete="off">
    <?= Helper\form_csrf() ?>
    <?= Helper\form_hidden('id', $values) ?>

    <?= Helper\form_label(t('Start date'), 'date_started') ?>
    <?= Helper\form_text('date_started', $values, array(), array('placeholder="'.Helper\in_list($date_format, $date_formats).'"'), 'form-date') ?>

    <?= Helper\form_label(t('Time estimated'), 'time_estimated') ?>
    <?= Helper\form_numeric('time_estimated', $values, array(), array('placeholder="'.t('hours').'"')) ?>

    <?= Helper\form_label(t('Time spent'), 'time_spent') ?>
    <?= Helper\form_numeric('time_spent', $values, array(), array('placeholder="'.t('hours').'"')) ?>

    <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
</form>