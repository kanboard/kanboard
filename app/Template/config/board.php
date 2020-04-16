<div class="page-header">
    <h2><?= t('Board settings') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('ConfigController', 'save', array('redirect' => 'board')) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>

    <fieldset>
        <?= $this->form->label(t('Task highlight period'), 'board_highlight_period') ?>
        <?= $this->form->number('board_highlight_period', $values, $errors) ?>
        <p class="form-help"><?= t('Period (in second) to consider a task was modified recently (0 to disable, 2 days by default)') ?></p>

        <?= $this->form->label(t('Refresh interval for public board'), 'board_public_refresh_interval') ?>
        <?= $this->form->number('board_public_refresh_interval', $values, $errors) ?>
        <p class="form-help"><?= t('Frequency in second (60 seconds by default)') ?></p>

        <?= $this->form->label(t('Refresh interval for personal board'), 'board_private_refresh_interval') ?>
        <?= $this->form->number('board_private_refresh_interval', $values, $errors) ?>
        <p class="form-help"><?= t('Frequency in second (0 to disable this feature, 10 seconds by default)') ?></p>
    </fieldset>

    <?= $this->hook->render('template:config:board', array('values' => $values, 'errors' => $errors)) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
    </div>
</form>
