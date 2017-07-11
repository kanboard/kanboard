<div class="page-header">
    <h2><?= t('New drag and drop restriction for the role "%s"', $role['role']) ?></h2>
</div>
<form method="post" action="<?= $this->url->href('ColumnMoveRestrictionController', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('project_id', $values) ?>
    <?= $this->form->hidden('role_id', $values) ?>

    <?= $this->form->label(t('Source column'), 'src_column_id') ?>
    <?= $this->form->select('src_column_id', $columns, $values, $errors) ?>

    <?= $this->form->label(t('Destination column'), 'dst_column_id') ?>
    <?= $this->form->select('dst_column_id', $columns, $values, $errors) ?>

    <?= $this->form->checkbox('only_assigned', t('Only for tasks assigned to the current user'), 1, isset($values['only_assigned']) && $values['only_assigned'] == 1) ?>

    <?= $this->modal->submitButtons() ?>

    <p class="alert alert-info"><?= t('People belonging to this role will be able to move tasks only between the source and the destination column.') ?></p>
</form>
