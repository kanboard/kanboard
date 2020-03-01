<div class="page-header">
    <h2><?= t('Allowed Groups') ?></h2>
</div>

<?php if (empty($groups)): ?>
    <div class="alert"><?= t('No group has been allowed.') ?></div>
<?php else: ?>
    <table class="table-scrolling">
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
                    <?= $this->app->component('project-select-role', array(
                        'roles' => $roles,
                        'role' => $group['role'],
                        'id' => $group['id'],
                        'url' => $this->url->to('ProjectPermissionController', 'changeGroupRole', array('project_id' => $project['id'])),
                    )) ?>
                </td>
                <td>
                    <?= $this->url->icon('trash-o', t('Remove'), 'ProjectPermissionController', 'removeGroup', array('project_id' => $project['id'], 'group_id' => $group['id']), true) ?>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>

<?php if ($project['is_private'] == 0): ?>
    <div class="panel">
        <form method="post" action="<?= $this->url->href('ProjectPermissionController', 'addGroup', array('project_id' => $project['id'])) ?>" autocomplete="off" class="form-inline">
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
                'data-dst-extra-fields="external_id"',
                'data-search-url="'.$this->url->href('GroupAjaxController', 'autocomplete').'"',
            ),
                'autocomplete') ?>

            <?= $this->form->select('role', $roles, $values, $errors) ?>

            <button type="submit" class="btn btn-blue"><?= t('Add') ?></button>
        </form>
    </div>
<?php endif ?>

