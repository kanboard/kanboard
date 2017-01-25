<div class="page-header">
    <h2><?= t('Application settings') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('ConfigController', 'save', array('redirect' => 'application')) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>

    <fieldset>
        <?= $this->form->label(t('Application URL'), 'application_url') ?>
        <?= $this->form->text('application_url', $values, $errors, array('placeholder="http://example.kanboard.net/"')) ?>
        <p class="form-help"><?= t('Example: http://example.kanboard.net/ (used to generate absolute URLs)') ?></p>

        <?= $this->form->label(t('Language'), 'application_language') ?>
        <?= $this->form->select('application_language', $languages, $values, $errors) ?>

        <?= $this->form->checkbox('password_reset', t('Enable "Forget Password"'), 1, $values['password_reset'] == 1) ?>
    </fieldset>

    <fieldset>
        <?= $this->form->label(t('Timezone'), 'application_timezone') ?>
        <?= $this->form->select('application_timezone', $timezones, $values, $errors) ?>

        <?= $this->form->label(t('Date format'), 'application_date_format') ?>
        <?= $this->form->select('application_date_format', $date_formats, $values, $errors) ?>
        <p class="form-help"><?= t('ISO format is always accepted, example: "%s" and "%s"', date('Y-m-d'), date('Y_m_d')) ?></p>

        <?= $this->form->label(t('Time format'), 'application_time_format') ?>
        <?= $this->form->select('application_time_format', $time_formats, $values, $errors) ?>
    </fieldset>

    <fieldset>
        <?= $this->form->label(t('Custom Stylesheet'), 'application_stylesheet') ?>
        <?= $this->form->textarea('application_stylesheet', $values, $errors) ?>
    </fieldset>

    <?= $this->hook->render('template:config:application', array('values' => $values, 'errors' => $errors)) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
    </div>
</form>
