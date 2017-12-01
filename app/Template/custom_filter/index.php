<div class="page-header">
    <h2><?= t('Custom filters') ?></h2>
    <ul>
        <li>
            <?= $this->modal->medium('filter', t('Add custom filters'), 'CustomFilterController', 'create', array('project_id' => $project['id'])) ?>
        </li>
    </ul>
</div>
<?php if (! empty($custom_filters)): ?>
    <table class="table-striped table-scrolling">
        <tr>
            <th><?= t('Name') ?></th>
            <th class="column-30"><?= t('Filter') ?></th>
            <th class="column-10"><?= t('Shared') ?></th>
            <th class="column-15"><?= t('Append/Replace') ?></th>
            <th class="column-20"><?= t('Owner') ?></th>
        </tr>
    <?php foreach ($custom_filters as $filter): ?>
         <tr>
            <td>
                <?php if (($filter['user_id'] == $this->user->getId() || $this->user->isAdmin() || $this->projectRole->getProjectUserRole($project['id']) == \Kanboard\Core\Security\Role::PROJECT_MANAGER) && $this->user->hasProjectAccess('CustomFilterController', 'edit', $project['id'])): ?>
                    <div class="dropdown">
                        <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog"></i><i class="fa fa-caret-down"></i></a>
                        <ul>
                            <li><?= $this->modal->medium('edit', t('Edit'), 'CustomFilterController', 'edit', array('project_id' => $filter['project_id'], 'filter_id' => $filter['id'])) ?></li>
                            <li><?= $this->modal->confirm('trash-o', t('Remove'), 'CustomFilterController', 'confirm', array('project_id' => $filter['project_id'], 'filter_id' => $filter['id'])) ?></li>
                        </ul>
                    </div>
                <?php endif ?>
                <?= $this->text->e($filter['name']) ?>
            </td>
            <td>
                <?= $this->text->e($filter['filter']) ?>
            </td>
            <td>
                <?php if ($filter['is_shared'] == 1): ?>
                    <?= t('Yes') ?>
                <?php else: ?>
                    <?= t('No') ?>
                <?php endif ?>
            </td>
            <td>
                <?php if ($filter['append'] == 1): ?>
                    <?= t('Append') ?>
                <?php else: ?>
                    <?= t('Replace') ?>
                <?php endif ?>
            </td>
            <td>
                <?= $this->text->e($filter['owner_name'] ?: $filter['owner_username']) ?>
            </td>
        </tr>
    <?php endforeach ?>
    </table>
<?php else: ?>
    <p class="alert"><?= t('There is no custom filter.') ?></p>
<?php endif ?>

