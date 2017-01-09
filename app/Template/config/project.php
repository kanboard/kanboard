<div class="page-header">
    <h2><?= t('Project settings') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('ConfigController', 'save', array('redirect' => 'project')) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>

    <fieldset>
        <?= $this->form->label(t('Default task color'), 'default_color') ?>
        <?= $this->form->select('default_color', $colors, $values, $errors) ?>

        <?= $this->form->label(t('Default columns for new projects (Comma-separated)'), 'board_columns') ?>
        <?= $this->form->text('board_columns', $values, $errors) ?>
        <p class="form-help"><?= t('Default values are "%s"', $default_columns) ?></p>

        <?= $this->form->label(t('Default categories for new projects (Comma-separated)'), 'project_categories') ?>
        <?= $this->form->text('project_categories', $values, $errors) ?>
        <p class="form-help"><?= t('Example: "Bug, Feature Request, Improvement"') ?></p>
    </fieldset>

    <fieldset>
        <?= $this->form->checkbox('disable_private_project', t('Disable private projects'), 1, isset($values['disable_private_project']) && $values['disable_private_project'] == 1) ?>
        <?= $this->form->checkbox('subtask_restriction', t('Allow only one subtask in progress at the same time for a user'), 1, $values['subtask_restriction'] == 1) ?>
        <?= $this->form->checkbox('subtask_time_tracking', t('Trigger automatically subtask time tracking'), 1, $values['subtask_time_tracking'] == 1) ?>
        <?= $this->form->checkbox('cfd_include_closed_tasks', t('Include closed tasks in the cumulative flow diagram'), 1, $values['cfd_include_closed_tasks'] == 1) ?>
    </fieldset>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
    </div>
</form>
