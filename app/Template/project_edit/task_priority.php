<div class="page-header">
    <h2><?= t('Edit project') ?></h2>
    <ul>
        <li ><?= $this->url->link(t('General'), 'ProjectEdit', 'edit', array('project_id' => $project['id']), false, 'popover-link') ?></li>
        <li><?= $this->url->link(t('Dates'), 'ProjectEdit', 'dates', array('project_id' => $project['id']), false, 'popover-link') ?></li>
        <li><?= $this->url->link(t('Description'), 'ProjectEdit', 'description', array('project_id' => $project['id']), false, 'popover-link') ?></li>
        <li class="active"><?= $this->url->link(t('Task priority'), 'ProjectEdit', 'priority', array('project_id' => $project['id']), false, 'popover-link') ?></li>
    </ul>
</div>
<form method="post" class="popover-form" action="<?= $this->url->href('ProjectEdit', 'update', array('project_id' => $project['id'], 'redirect' => 'priority')) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('id', $values) ?>
    <?= $this->form->hidden('name', $values) ?>

    <?= $this->form->label(t('Default priority'), 'priority_default') ?>
    <?= $this->form->number('priority_default', $values, $errors) ?>

    <?= $this->form->label(t('Lowest priority'), 'priority_start') ?>
    <?= $this->form->number('priority_start', $values, $errors) ?>

    <?= $this->form->label(t('Highest priority'), 'priority_end') ?>
    <?= $this->form->number('priority_end', $values, $errors) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
    </div>
</form>

<p class="alert alert-info"><?= t('If you put zero to the low and high priority, this feature will be disabled.') ?></p>
