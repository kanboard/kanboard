<section id="main">
    <div class="page-header">
        <h2><?= $title ?></h2>
    </div>
    <form id="project-creation-form" method="post" action="<?= $this->url->href('ProjectCreationController', 'save') ?>" autocomplete="off">

        <?= $this->form->csrf() ?>
        <?= $this->form->hidden('is_private', $values) ?>

        <?= $this->form->label(t('Name'), 'name') ?>
        <?= $this->form->text('name', $values, $errors, array('autofocus', 'required', 'maxlength="50"')) ?>

        <?php if (count($projects_list) > 1): ?>
            <?= $this->form->label(t('Create from another project'), 'src_project_id') ?>
            <?= $this->form->select('src_project_id', $projects_list, $values, array(), array(), 'js-project-creation-select-options') ?>
        <?php endif ?>

        <div class="js-project-creation-options" <?= isset($values['src_project_id']) && $values['src_project_id'] > 0 ? '' : 'style="display: none"' ?>>
            <p class="alert"><?= t('Which parts of the project do you want to duplicate?') ?></p>

            <?php if (! $is_private): ?>
                <?= $this->form->checkbox('projectPermissionModel', t('Permissions'), 1, true) ?>
            <?php endif ?>

            <?= $this->form->checkbox('categoryModel', t('Categories'), 1, true) ?>
            <?= $this->form->checkbox('tagDuplicationModel', t('Tags'), 1, true) ?>
            <?= $this->form->checkbox('actionModel', t('Actions'), 1, true) ?>
            <?= $this->form->checkbox('projectTaskDuplicationModel', t('Tasks'), 1, false) ?>
        </div>

        <?= $this->modal->submitButtons() ?>
    </form>
    <?php if ($is_private): ?>
    <div class="alert alert-info">
        <p><?= t('There is no user management for private projects.') ?></p>
    </div>
    <?php endif ?>
</section>
