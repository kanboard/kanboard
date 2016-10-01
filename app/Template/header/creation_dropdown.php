<?php $has_project_creation_access = $this->user->hasAccess('ProjectCreationController', 'create'); ?>
<?php $is_private_project_enabled = $this->app->config('disable_private_project', 0) == 0; ?>

<?php if ($has_project_creation_access || (!$has_project_creation_access && $is_private_project_enabled)): ?>
    <div class="dropdown">
        <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-plus fa-fw"></i><i class="fa fa-caret-down"></i></a>
        <ul>
            <?php if ($has_project_creation_access): ?>
                <li><i class="fa fa-plus fa-fw"></i>
                    <?= $this->url->link(t('New project'), 'ProjectCreationController', 'create', array(), false, 'popover') ?>
                </li>
            <?php endif ?>
            <?php if ($is_private_project_enabled): ?>
                <li>
                    <i class="fa fa-lock fa-fw"></i>
                    <?= $this->url->link(t('New private project'), 'ProjectCreationController', 'createPrivate', array(), false, 'popover') ?>
                </li>
            <?php endif ?>
            <?= $this->hook->render('template:header:creation-dropdown') ?>
        </ul>
    </div>
<?php endif ?>
