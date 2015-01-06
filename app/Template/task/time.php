<form method="post" action="<?= $this->u('task', 'time', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" class="form-inline task-time-form" autocomplete="off">
    <?= $this->formCsrf() ?>
    <?= $this->formHidden('id', $values) ?>

    <?= $this->formLabel(t('Start date'), 'date_started') ?>
    <?= $this->formText('date_started', $values, array(), array('placeholder="'.$this->inList($date_format, $date_formats).'"'), 'form-date') ?>

    <?= $this->formLabel(t('Time estimated'), 'time_estimated') ?>
    <?= $this->formNumeric('time_estimated', $values, array(), array('placeholder="'.t('hours').'"')) ?>

    <?= $this->formLabel(t('Time spent'), 'time_spent') ?>
    <?= $this->formNumeric('time_spent', $values, array(), array('placeholder="'.t('hours').'"')) ?>

    <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
</form>