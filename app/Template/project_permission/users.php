<?php if (empty($users)): ?>
    <div class="alert"><?= t('No specific user has been allowed.') ?></div>
<?php else: ?>
    <table class="table-scrolling">
        <tr>
            <th class="column-50"><?= t('User') ?></th>
            <th><?= t('Role') ?></th>
            <?php if ($project['is_private'] == 0): ?>
                <th class="column-15"><?= t('Actions') ?></th>
            <?php endif ?>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $this->text->e($user['name'] ?: $user['username']) ?></td>
                <td>
                    <?= $this->app->component('project-select-role', array(
                        'roles' => $roles,
                        'role' => $user['role'],
                        'id' => $user['id'],
                        'url' => $this->url->to('ProjectPermissionController', 'changeUserRole', array('project_id' => $project['id'])),
                    )) ?>
                </td>
                <td>
                    <?php if (! $this->user->isCurrentUser($user['id'])): ?>
                        <?= $this->url->icon('trash-o', t('Remove'), 'ProjectPermissionController', 'removeUser', array('project_id' => $project['id'], 'user_id' => $user['id']), true) ?>
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>

<?php if ($project['is_private'] == 0): ?>
    <div class="panel">
        <form method="post" action="<?= $this->url->href('ProjectPermissionController', 'addUser', array('project_id' => $project['id'])) ?>" autocomplete="off" class="form-inline">
            <?= $this->form->csrf() ?>
            <?= $this->form->hidden('project_id', array('project_id' => $project['id'])) ?>
            <?= $this->form->hidden('user_id', $values) ?>
            <?= $this->form->hidden('username', $values) ?>
            <?= $this->form->hidden('external_id', $values) ?>
            <?= $this->form->hidden('external_id_column', $values) ?>

            <?= $this->form->label(t('Name'), 'name') ?>
            <?= $this->form->text('name', $values, $errors, array(
                    'required',
                    'placeholder="'.t('Enter user name...').'"',
                    'title="'.t('Enter user name...').'"',
                    'data-dst-field="user_id"',
                    'data-dst-extra-fields="external_id,external_id_column,username"',
                    'data-search-url="'.$this->url->href('UserAjaxController', 'autocomplete').'"',
                ),
                'autocomplete') ?>

            <?= $this->form->select('role', $roles, $values, $errors) ?>

            <button type="submit" class="btn btn-blue"><?= t('Add') ?></button>
        </form>
    </div>
    <?= $this->hook->render('template:project-permission:after-adduser', ['project' => $project, 'values' => $values, 'errors' => $errors]) ?>
<?php endif ?>
