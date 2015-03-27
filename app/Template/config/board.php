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

    <?= $this->formLabel(t('Default columns for new projects (Comma-separated)'), 'board_columns') ?>
    <?= $this->formText('board_columns', $values, $errors) ?><br/>
    <p class="form-help"><?= t('Default values are "%s"', $default_columns) ?></p>

    <?= $this->formLabel(t('Default categories for new projects (Comma-separated)'), 'project_categories') ?>
    <?= $this->formText('project_categories', $values, $errors) ?><br/>
    <p class="form-help"><?= t('Example: "Bug, Feature Request, Improvement"') ?></p>

    <?= $this->formCheckbox('subtask_restriction', t('Allow only one subtask in progress at the same time for a user'), 1, $values['subtask_restriction'] == 1) ?>
    <?= $this->formCheckbox('subtask_time_tracking', t('Enable time tracking for subtasks'), 1, $values['subtask_time_tracking'] == 1) ?>
    <?= $this->formCheckbox('subtask_forecast', t('Show subtask estimates in the user calendar'), 1, $values['subtask_forecast'] == 1) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>
</section>