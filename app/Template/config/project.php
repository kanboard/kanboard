<div class="page-header">
    <h2><?= t('Project settings') ?></h2>
</div>
<section>
<form method="post" action="<?= $this->u('config', 'project') ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

    <?= $this->formLabel(t('Default columns for new projects (Comma-separated)'), 'board_columns') ?>
    <?= $this->formText('board_columns', $values, $errors) ?><br/>
    <p class="form-help"><?= t('Default values are "%s"', $default_columns) ?></p>

    <?= $this->formLabel(t('Default categories for new projects (Comma-separated)'), 'project_categories') ?>
    <?= $this->formText('project_categories', $values, $errors) ?><br/>
    <p class="form-help"><?= t('Example: "Bug, Feature Request, Improvement"') ?></p>

    <?= $this->formCheckbox('subtask_restriction', t('Allow only one subtask in progress at the same time for a user'), 1, $values['subtask_restriction'] == 1) ?>
    <?= $this->formCheckbox('subtask_time_tracking', t('Enable time tracking for subtasks'), 1, $values['subtask_time_tracking'] == 1) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>
</section>