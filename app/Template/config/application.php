<div class="page-header">
    <h2><?= t('Application settings') ?></h2>
</div>
<section>
<form method="post" action="<?= $this->u('config', 'application') ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

    <?= $this->formLabel(t('Application URL'), 'application_url') ?>
    <?= $this->formText('application_url', $values, $errors, array('placeholder="http://example.kanboard.net/"')) ?><br/>
    <p class="form-help"><?= t('Example: http://example.kanboard.net/ (used by email notifications)') ?></p>

    <?= $this->formLabel(t('Language'), 'application_language') ?>
    <?= $this->formSelect('application_language', $languages, $values, $errors) ?><br/>

    <?= $this->formLabel(t('Timezone'), 'application_timezone') ?>
    <?= $this->formSelect('application_timezone', $timezones, $values, $errors) ?><br/>

    <?= $this->formLabel(t('Date format'), 'application_date_format') ?>
    <?= $this->formSelect('application_date_format', $date_formats, $values, $errors) ?><br/>
    <p class="form-help"><?= t('ISO format is always accepted, example: "%s" and "%s"', date('Y-m-d'), date('Y_m_d')) ?></p>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>
</section>