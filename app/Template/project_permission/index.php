<div class="page-header">
    <h2><?= t('Allowed Users') ?></h2>
</div>

<?php if ($project['is_everybody_allowed']): ?>
    <div class="alert"><?= t('Everybody have access to this project.') ?></div>
<?php else: ?>
    <?= $this->render('project_permission/users', array(
        'project' => $project,
        'roles' => $roles,
        'users' => $users,
        'errors' => $errors,
        'values' => $values,
    )) ?>

    <?= $this->render('project_permission/groups', array(
        'project' => $project,
        'roles' => $roles,
        'groups' => $groups,
        'errors' => $errors,
        'values' => $values,
    )) ?>
<?php endif ?>

<?php if ($project['is_private'] == 0): ?>
<hr/>
<form method="post" action="<?= $this->url->href('ProjectPermissionController', 'allowEverybody', array('project_id' => $project['id'])) ?>">
    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('id', array('id' => $project['id'])) ?>
    <?= $this->form->checkbox('is_everybody_allowed', t('Allow everybody to access to this project'), 1, $project['is_everybody_allowed']) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
    </div>
</form>
<?php endif ?>
