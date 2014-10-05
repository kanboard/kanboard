<div class="page-header">
    <h2><?= t('Application settings') ?></h2>
</div>
<section>
<form method="post" action="<?= Helper\u('config', 'application') ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>

    <?= Helper\form_label(t('Application URL'), 'application_url') ?>
    <?= Helper\form_text('application_url', $values, $errors, array('placeholder="http://example.kanboar.net/"')) ?><br/>
    <p class="form-help"><?= t('Example: http://example.kanboard.net/ (used by email notifications)') ?></p>

    <?= Helper\form_label(t('Language'), 'application_language') ?>
    <?= Helper\form_select('application_language', $languages, $values, $errors) ?><br/>

    <?= Helper\form_label(t('Timezone'), 'application_timezone') ?>
    <?= Helper\form_select('application_timezone', $timezones, $values, $errors) ?><br/>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>
</section>