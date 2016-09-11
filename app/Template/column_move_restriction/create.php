<section id="main">
    <div class="page-header">
        <h2><?= t('New column restriction for the role "%s"', $role['role']) ?></h2>
    </div>
    <form class="popover-form" method="post" action="<?= $this->url->href('ColumnMoveRestrictionController', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">
        <?= $this->form->csrf() ?>
        <?= $this->form->hidden('project_id', $values) ?>
        <?= $this->form->hidden('role_id', $values) ?>

        <?= $this->form->label(t('Source column'), 'src_column_id') ?>
        <?= $this->form->select('src_column_id', $columns, $values, $errors) ?>

        <?= $this->form->label(t('Destination column'), 'dst_column_id') ?>
        <?= $this->form->select('dst_column_id', $columns, $values, $errors) ?>

        <div class="form-actions">
            <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
            <?= t('or') ?>
            <?= $this->url->link(t('cancel'), 'ProjectRoleController', 'show', array(), false, 'close-popover') ?>
        </div>

        <p class="alert alert-info"><?= t('People belonging to this role won\'t be able to move tasks between the source and the destination column.') ?></p>
    </form>
</section>
