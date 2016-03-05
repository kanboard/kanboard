<div class="page-header">
    <h2><?= t('Tasks exportation for "%s"', $project['name']) ?></h2>
</div>

<p class="alert alert-info"><?= t('This report contains all tasks information for the given date range.') ?></p>

<form method="get" action="?" autocomplete="off">

    <?= $this->form->hidden('controller', $values) ?>
    <?= $this->form->hidden('action', $values) ?>
    <?= $this->form->hidden('project_id', $values) ?>

    <?= $this->form->label(t('Start Date'), 'from') ?>
    <?= $this->form->text('from', $values, $errors, array('required', 'placeholder="'.$this->text->in($date_format, $date_formats).'"'), 'form-date') ?>

    <?= $this->form->label(t('End Date'), 'to') ?>
    <?= $this->form->text('to', $values, $errors, array('required', 'placeholder="'.$this->text->in($date_format, $date_formats).'"'), 'form-date') ?>

    <div class="form-help"><?= t('Others formats accepted: %s and %s', date('Y-m-d'), date('Y_m_d')) ?></div>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Execute') ?></button>
    </div>
</form>