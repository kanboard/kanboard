<section id="main">
    <div class="page-header">
        <h2><?= t('New project restriction for the role "%s"', $role['role']) ?></h2>
    </div>
    <form class="popover-form" method="post" action="<?= $this->url->href('ProjectRoleRestrictionController', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">
        <?= $this->form->csrf() ?>
        <?= $this->form->hidden('project_id', $values) ?>
        <?= $this->form->hidden('role_id', $values) ?>

        <?= $this->form->label(t('Restriction'), 'rule') ?>
        <?= $this->form->select('rule', $restrictions, $values, $errors) ?>

        <div class="form-actions">
            <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
            <?= t('or') ?>
            <?= $this->url->link(t('cancel'), 'ProjectRoleController', 'show', array(), false, 'close-popover') ?>
        </div>
    </form>
</section>
