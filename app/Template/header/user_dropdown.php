<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><?= $this->avatar->currentUserSmall('avatar-inline') ?><i class="fa fa-caret-down"></i></a>
    <ul>
        <li class="no-hover"><strong><?= $this->text->e($this->user->getFullname()) ?></strong></li>
        <li>
            <i class="fa fa-tachometer fa-fw"></i>
            <?= $this->url->link(t('My dashboard'), 'DashboardController', 'show', array('user_id' => $this->user->getId())) ?>
        </li>
        <li>
            <i class="fa fa-home fa-fw"></i>
            <?= $this->url->link(t('My profile'), 'UserViewController', 'show', array('user_id' => $this->user->getId())) ?>
        </li>
        <li>
            <i class="fa fa-folder fa-fw"></i>
            <?= $this->url->link(t('Projects management'), 'ProjectListController', 'show') ?>
        </li>
        <?php if ($this->user->hasAccess('UserListController', 'show')): ?>
            <li>
                <i class="fa fa-user fa-fw"></i>
                <?= $this->url->link(t('Users management'), 'UserListController', 'show') ?>
            </li>
            <li>
                <i class="fa fa-group fa-fw"></i>
                <?= $this->url->link(t('Groups management'), 'GroupListController', 'index') ?>
            </li>
            <li>
                <i class="fa fa-cubes" aria-hidden="true"></i>
                <?= $this->url->link(t('Plugins'), 'PluginController', 'show') ?>
            </li>
            <li>
                <i class="fa fa-cog fa-fw"></i>
                <?= $this->url->link(t('Settings'), 'ConfigController', 'index') ?>
            </li>
        <?php endif ?>

        <?= $this->hook->render('template:header:dropdown') ?>

        <li>
            <i class="fa fa-life-ring fa-fw"></i>
            <?= $this->url->link(t('Documentation'), 'DocumentationController', 'show') ?>
        </li>
        <?php if (! DISABLE_LOGOUT): ?>
            <li>
                <i class="fa fa-sign-out fa-fw"></i>
                <?= $this->url->link(t('Logout'), 'AuthController', 'logout') ?>
            </li>
        <?php endif ?>
    </ul>
</div>
