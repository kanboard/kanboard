<div class="page-header">
    <h2><?= t('Allowed Users') ?></h2>
</div>

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
