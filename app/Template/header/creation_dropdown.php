<?php $has_project_creation_access = $this->user->hasAccess('ProjectCreationController', 'create'); ?>
<?php $is_private_project_enabled = $this->app->config('disable_private_project', 0) == 0; ?>

<?php if ($has_project_creation_access || (!$has_project_creation_access && $is_private_project_enabled)): ?>
    <div class="dropdown header-creation-menu">
        <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-plus fa-fw"></i><i class="fa fa-caret-down"></i></a>
        <ul>
            <?php if ($has_project_creation_access): ?>
                <li>
                    <?= $this->modal->medium('plus', t('New project'), 'ProjectCreationController', 'create') ?>
                </li>
            <?php endif ?>
            <?php if ($is_private_project_enabled): ?>
                <li>
                    <?= $this->modal->medium('lock', t('New personal project'), 'ProjectCreationController', 'createPrivate') ?>
                </li>
            <?php endif ?>
            <?= $this->hook->render('template:header:creation-dropdown') ?>
        </ul>
    </div>
<?php endif ?>
