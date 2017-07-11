<div class="page-header">
    <h2><?= t('Custom Project Roles') ?></h2>
    <ul>
        <li>
            <?= $this->modal->medium('plus', t('Add a new custom role'), 'ProjectRoleController', 'create', array('project_id' => $project['id'])) ?>
        </li>
    </ul>
</div>

<?php if (empty($roles)): ?>
    <div class="alert"><?= t('There is no custom role for this project.') ?></div>
<?php else: ?>
    <?php foreach ($roles as $role): ?>
    <table class="table-striped">
        <tr>
            <th>
                <div class="dropdown">
                    <a href="#" class="dropdown-menu"><?= t('Restrictions for the role "%s"', $role['role']) ?> <i class="fa fa-caret-down"></i></a>
                    <ul>
                        <li>
                            <?= $this->modal->medium('plus', t('Add a new project restriction'), 'ProjectRoleRestrictionController', 'create', array('project_id' => $project['id'], 'role_id' => $role['role_id'])) ?>
                        </li>
                        <li>
                            <?= $this->modal->medium('plus', t('Add a new drag and drop restriction'), 'ColumnMoveRestrictionController', 'create', array('project_id' => $project['id'], 'role_id' => $role['role_id'])) ?>
                        </li>
                        <li>
                            <?= $this->modal->medium('plus', t('Add a new column restriction'), 'ColumnRestrictionController', 'create', array('project_id' => $project['id'], 'role_id' => $role['role_id'])) ?>
                        </li>
                        <li>
                            <?= $this->modal->medium('edit', t('Edit this role'), 'ProjectRoleController', 'edit', array('project_id' => $project['id'], 'role_id' => $role['role_id'])) ?>
                        </li>
                        <li>
                            <?= $this->modal->confirm('trash-o', t('Remove this role'), 'ProjectRoleController', 'confirm', array('project_id' => $project['id'], 'role_id' => $role['role_id'])) ?>
                        </li>
                    </ul>
                </div>
            </th>
            <th class="column-15">
                <?= t('Actions') ?>
            </th>
        </tr>
        <?php if (empty($role['project_restrictions']) && empty($role['column_restrictions']) && empty($role['column_move_restrictions'])): ?>
            <tr>
                <td colspan="2"><?= t('There is no restriction for this role.') ?></td>
            </tr>
        <?php else: ?>
            <?php foreach ($role['project_restrictions'] as $restriction): ?>
                <tr>
                    <td>
                        <i class="fa fa-ban fa-fw" aria-hidden="true"></i>
                        <strong><?= t('Project') ?></strong>
                        <i class="fa fa-arrow-right fa-fw" aria-hidden="true"></i>
                        <?= $this->text->e($restriction['title']) ?>
                    </td>
                    <td>
                        <?= $this->modal->confirm('trash-o', t('Remove'), 'ProjectRoleRestrictionController', 'confirm', array('project_id' => $project['id'], 'restriction_id' => $restriction['restriction_id'])) ?>
                    </td>
                </tr>
            <?php endforeach ?>
            <?php foreach ($role['column_restrictions'] as $restriction): ?>
                <tr>
                    <td>
                        <?php if (strpos($restriction['rule'], 'block') === 0): ?>
                            <i class="fa fa-ban fa-fw" aria-hidden="true"></i>
                        <?php else: ?>
                            <i class="fa fa-check-circle-o fa-fw" aria-hidden="true"></i>
                        <?php endif ?>
                        <strong><?= $this->text->e($restriction['column_title']) ?></strong>
                        <i class="fa fa-arrow-right fa-fw" aria-hidden="true"></i>
                        <?= $this->text->e($restriction['title']) ?>
                    </td>
                    <td>
                        <?= $this->modal->confirm('trash-o', t('Remove'), 'ColumnRestrictionController', 'confirm', array('project_id' => $project['id'], 'restriction_id' => $restriction['restriction_id'])) ?>
                    </td>
                </tr>
            <?php endforeach ?>
            <?php foreach ($role['column_move_restrictions'] as $restriction): ?>
                <tr>
                    <td>
                        <i class="fa fa-check-circle-o fa-fw" aria-hidden="true"></i>
                        <strong><?= $this->text->e($restriction['src_column_title']) ?> / <?= $this->text->e($restriction['dst_column_title']) ?></strong>
                        <i class="fa fa-arrow-right fa-fw" aria-hidden="true"></i>
                        <?php if ($restriction['only_assigned'] == 1): ?>
                            <?= t('Only moving task between those columns is permitted for tasks assigned to the current user') ?>
                        <?php else: ?>
                            <?= t('Only moving task between those columns is permitted') ?>
                        <?php endif ?>
                    </td>
                    <td>
                        <?= $this->modal->confirm('trash-o', t('Remove'), 'ColumnMoveRestrictionController', 'confirm', array('project_id' => $project['id'], 'restriction_id' => $restriction['restriction_id'])) ?>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php endif ?>
    </table>
    <?php endforeach ?>
<?php endif ?>
