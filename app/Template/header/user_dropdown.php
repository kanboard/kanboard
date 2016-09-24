<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><?= $this->avatar->currentUserSmall('avatar-inline') ?><i class="fa fa-caret-down"></i></a>
    <ul>
        <li class="no-hover"><strong><?= $this->text->e($this->user->getFullname()) ?></strong></li>
        <li>
            <?= $this->url->link('<i class="fa fa-tachometer fa-fw"></i>' . t('My dashboard'), 'DashboardController', 'show', array('user_id' => $this->user->getId())) ?>
        </li>
        <li>
            <?= $this->url->link('<i class="fa fa-home fa-fw"></i>' . t('My profile'), 'UserViewController', 'show', array('user_id' => $this->user->getId())) ?>
        </li>
        <li>
            <?= $this->url->link('<i class="fa fa-folder fa-fw"></i>' . t('Projects management'), 'ProjectListController', 'show') ?>
        </li>
        <?php if ($this->user->hasAccess('UserListController', 'show')): ?>
            <li>
                <?= $this->url->link('<i class="fa fa-user fa-fw"></i>' . t('Users management'), 'UserListController', 'show') ?>
            </li>
            <li>
                <?= $this->url->link('<i class="fa fa-group fa-fw"></i>' . t('Groups management'), 'GroupListController', 'index') ?>
            </li>
            <li>
                <?= $this->url->link('<i class="fa fa-cubes" aria-hidden="true"></i>' . t('Plugins'), 'PluginController', 'show') ?>
            </li>
            <li>
                <?= $this->url->link('<i class="fa fa-cog fa-fw"></i>' . t('Settings'), 'ConfigController', 'index') ?>
            </li>
        <?php endif ?>

        <?= $this->hook->render('template:header:dropdown') ?>

        <li>
            <?= $this->url->link('<i class="fa fa-life-ring fa-fw"></i>' . t('Documentation'), 'DocumentationController', 'show') ?>
        </li>
        <?php if (! DISABLE_LOGOUT): ?>
            <li>
                <?= $this->url->link('<i class="fa fa-sign-out fa-fw"></i>' . t('Logout'), 'AuthController', 'logout') ?>
            </li>
        <?php endif ?>
    </ul>
</div>
