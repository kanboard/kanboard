<div class="page-header">
    <h2><?= t('Board settings') ?></h2>
</div>
<section>
<form method="post" action="<?= $this->u('config', 'board') ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

    <?= $this->formLabel(t('Task highlight period'), 'board_highlight_period') ?>
    <?= $this->formNumber('board_highlight_period', $values, $errors) ?><br/>
    <p class="form-help"><?= t('Period (in second) to consider a task was modified recently (0 to disable, 2 days by default)') ?></p>

    <?= $this->formLabel(t('Refresh interval for public board'), 'board_public_refresh_interval') ?>
    <?= $this->formNumber('board_public_refresh_interval', $values, $errors) ?><br/>
    <p class="form-help"><?= t('Frequency in second (60 seconds by default)') ?></p>

    <?= $this->formLabel(t('Refresh interval for private board'), 'board_private_refresh_interval') ?>
    <?= $this->formNumber('board_private_refresh_interval', $values, $errors) ?><br/>
    <p class="form-help"><?= t('Frequency in second (0 to disable this feature, 10 seconds by default)') ?></p>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>
</section>