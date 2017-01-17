<div class="page-header">
    <h2><?= t('Edit custom project role') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('ProjectRoleController', 'update', array('project_id' => $project['id'], 'role_id' => $role['role_id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('project_id', $values) ?>
    <?= $this->form->hidden('role_id', $values) ?>

    <?= $this->form->label(t('Role'), 'role') ?>
    <?= $this->form->text('role', $values, $errors, array('autofocus', 'required', 'maxlength="50"')) ?>

    <?= $this->modal->submitButtons() ?>
</form>
