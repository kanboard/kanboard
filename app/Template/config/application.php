<div class="page-header">
    <h2><?= t('Application settings') ?></h2>
</div>
<section>
<form method="post" action="<?= $this->url->href('config', 'application') ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <?= $this->form->label(t('Application URL'), 'application_url') ?>
    <?= $this->form->text('application_url', $values, $errors, array('placeholder="http://example.kanboard.net/"')) ?><br/>
    <p class="form-help"><?= t('Example: http://example.kanboard.net/ (used by email notifications)') ?></p>

    <?= $this->form->label(t('Language'), 'application_language') ?>
    <?= $this->form->select('application_language', $languages, $values, $errors) ?><br/>

    <?= $this->form->label(t('Timezone'), 'application_timezone') ?>
    <?= $this->form->select('application_timezone', $timezones, $values, $errors) ?><br/>

    <?= $this->form->label(t('Date format'), 'application_date_format') ?>
    <?= $this->form->select('application_date_format', $date_formats, $values, $errors) ?><br/>
    <p class="form-help"><?= t('ISO format is always accepted, example: "%s" and "%s"', date('Y-m-d'), date('Y_m_d')) ?></p>

    <?= $this->form->label(t('Custom Stylesheet'), 'application_stylesheet') ?>
    <?= $this->form->textarea('application_stylesheet', $values, $errors) ?><br/>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>
</section>