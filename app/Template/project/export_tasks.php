<div class="page-header">
    <h2>
        <?= t('Tasks exportation for "%s"', $project['name']) ?>
    </h2>
</div>

<form method="get" action="?" autocomplete="off">

    <?= $this->formHidden('controller', $values) ?>
    <?= $this->formHidden('action', $values) ?>
    <?= $this->formHidden('project_id', $values) ?>

    <?= $this->formLabel(t('Start Date'), 'from') ?>
    <?= $this->formText('from', $values, $errors, array('required', 'placeholder="'.$this->inList($date_format, $date_formats).'"'), 'form-date') ?><br/>

    <?= $this->formLabel(t('End Date'), 'to') ?>
    <?= $this->formText('to', $values, $errors, array('required', 'placeholder="'.$this->inList($date_format, $date_formats).'"'), 'form-date') ?>

    <div class="form-help"><?= t('Others formats accepted: %s and %s', date('Y-m-d'), date('Y_m_d')) ?></div>

    <div class="form-actions">
        <input type="submit" value="<?= t('Execute') ?>" class="btn btn-blue"/>
    </div>
</form>