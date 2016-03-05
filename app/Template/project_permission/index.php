<div class="page-header">
    <h2><?= t('Allowed Users') ?></h2>
</div>

<?php if ($project['is_everybody_allowed']): ?>
    <div class="alert"><?= t('Everybody have access to this project.') ?></div>
<?php else: ?>

    <?php if (empty($users)): ?>
        <div class="alert"><?= t('No user have been allowed specifically.') ?></div>
    <?php else: ?>
        <table>
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
                    <?= $this->form->select(
                        'role-'.$user['id'],
                        $roles,
                        array('role-'.$user['id'] => $user['role']),
                        array(),
                        array('data-url="'.$this->url->href('ProjectPermission', 'changeUserRole', array('project_id' => $project['id'])).'"', 'data-id="'.$user['id'].'"'),
                        'project-change-role'
                    ) ?>
                </td>
                <td>
                    <?= $this->url->link(t('Remove'), 'ProjectPermission', 'removeUser', array('project_id' => $project['id'], 'user_id' => $user['id']), true) ?>
                </td>
            </tr>
            <?php endforeach ?>
        </table>
    <?php endif ?>

    <?php if ($project['is_private'] == 0): ?>
    <div class="listing">
        <form method="post" action="<?= $this->url->href('ProjectPermission', 'addUser', array('project_id' => $project['id'])) ?>" autocomplete="off" class="form-inline">
            <?= $this->form->csrf() ?>
            <?= $this->form->hidden('project_id', array('project_id' => $project['id'])) ?>
            <?= $this->form->hidden('user_id', $values) ?>

            <?= $this->form->label(t('Name'), 'name') ?>
            <?= $this->form->text('name', $values, $errors, array(
                'required',
                'placeholder="'.t('Enter user name...').'"',
                'title="'.t('Enter user name...').'"',
                'data-dst-field="user_id"',
                'data-search-url="'.$this->url->href('UserHelper', 'autocomplete').'"',
            ),
            'autocomplete') ?>

            <?= $this->form->select('role', $roles, $values, $errors) ?>

            <button type="submit" class="btn btn-blue"><?= t('Add') ?></button>
        </form>
    </div>
    <?php endif ?>

    <div class="page-header">
        <h2><?= t('Allowed Groups') ?></h2>
    </div>

    <?php if (empty($groups)): ?>
        <div class="alert"><?= t('No group have been allowed specifically.') ?></div>
    <?php else: ?>
        <table>
            <tr>
                <th class="column-50"><?= t('Group') ?></th>
                <th><?= t('Role') ?></th>
                <?php if ($project['is_private'] == 0): ?>
                    <th class="column-15"><?= t('Actions') ?></th>
                <?php endif ?>
            </tr>
            <?php foreach ($groups as $group): ?>
            <tr>
                <td><?= $this->text->e($group['name']) ?></td>
                <td>
                    <?= $this->form->select(
                        'role-'.$group['id'],
                        $roles,
                        array('role-'.$group['id'] => $group['role']),
                        array(),
                        array('data-url="'.$this->url->href('ProjectPermission', 'changeGroupRole', array('project_id' => $project['id'])).'"', 'data-id="'.$group['id'].'"'),
                        'project-change-role'
                    ) ?>
                </td>
                <td>
                    <?= $this->url->link(t('Remove'), 'ProjectPermission', 'removeGroup', array('project_id' => $project['id'], 'group_id' => $group['id']), true) ?>
                </td>
            </tr>
            <?php endforeach ?>
        </table>
    <?php endif ?>

    <?php if ($project['is_private'] == 0): ?>
    <div class="listing">
        <form method="post" action="<?= $this->url->href('ProjectPermission', 'addGroup', array('project_id' => $project['id'])) ?>" autocomplete="off" class="form-inline">
            <?= $this->form->csrf() ?>
            <?= $this->form->hidden('project_id', array('project_id' => $project['id'])) ?>
            <?= $this->form->hidden('group_id', $values) ?>
            <?= $this->form->hidden('external_id', $values) ?>

            <?= $this->form->label(t('Group Name'), 'name') ?>
            <?= $this->form->text('name', $values, $errors, array(
                'required',
                'placeholder="'.t('Enter group name...').'"',
                'title="'.t('Enter group name...').'"',
                'data-dst-field="group_id"',
                'data-dst-extra-field="external_id"',
                'data-search-url="'.$this->url->href('GroupHelper', 'autocomplete').'"',
            ),
            'autocomplete') ?>

            <?= $this->form->select('role', $roles, $values, $errors) ?>

            <button type="submit" class="btn btn-blue"><?= t('Add') ?></button>
        </form>
    </div>
    <?php endif ?>

<?php endif ?>

<?php if ($project['is_private'] == 0): ?>
<hr/>
<form method="post" action="<?= $this->url->href('ProjectPermission', 'allowEverybody', array('project_id' => $project['id'])) ?>">
    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('id', array('id' => $project['id'])) ?>
    <?= $this->form->checkbox('is_everybody_allowed', t('Allow everybody to access to this project'), 1, $project['is_everybody_allowed']) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
    </div>
</form>
<?php endif ?>
