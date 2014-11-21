<div class="page-header">
    <h2><?= t('Board settings') ?></h2>
</div>
<section>
<form method="post" action="<?= Helper\u('config', 'board') ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>

    <?= Helper\form_label(t('Task highlight period'), 'board_highlight_period') ?>
    <?= Helper\form_number('board_highlight_period', $values, $errors) ?><br/>
    <p class="form-help"><?= t('Period (in second) to consider a task was modified recently (0 to disable, 2 days by default)') ?></p>

    <?= Helper\form_label(t('Refresh interval for public board'), 'board_public_refresh_interval') ?>
    <?= Helper\form_number('board_public_refresh_interval', $values, $errors) ?><br/>
    <p class="form-help"><?= t('Frequency in second (60 seconds by default)') ?></p>

    <?= Helper\form_label(t('Refresh interval for private board'), 'board_private_refresh_interval') ?>
    <?= Helper\form_number('board_private_refresh_interval', $values, $errors) ?><br/>
    <p class="form-help"><?= t('Frequency in second (0 to disable this feature, 10 seconds by default)') ?></p>

    <?= Helper\form_label(t('Default columns for new projects (Comma-separated)'), 'board_columns') ?>
    <?= Helper\form_text('board_columns', $values, $errors) ?><br/>
    <p class="form-help"><?= t('Default values are "%s"', $default_columns) ?></p>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>
</section>