<section id="main">
    <div class="page-header">
        <h2><?= t('New custom project role') ?></h2>
    </div>
    <form class="popover-form" method="post" action="<?= $this->url->href('ProjectRoleController', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">
        <?= $this->form->csrf() ?>
        <?= $this->form->hidden('project_id', $values) ?>

        <?= $this->form->label(t('Role'), 'role') ?>
        <?= $this->form->text('role', $values, $errors, array('autofocus', 'required', 'maxlength="50"')) ?>

        <div class="form-actions">
            <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
            <?= t('or') ?>
            <?= $this->url->link(t('cancel'), 'ProjectRoleController', 'show', array(), false, 'close-popover') ?>
        </div>
    </form>
</section>
