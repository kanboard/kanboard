<div class="page-header">
    <h2><?= t('New custom project role') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('ProjectRoleController', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('project_id', $values) ?>

    <?= $this->form->label(t('Role'), 'role') ?>
    <?= $this->form->text('role', $values, $errors, array('autofocus', 'required', 'maxlength="50"')) ?>

    <?= $this->modal->submitButtons() ?>
</form>
