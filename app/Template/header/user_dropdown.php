<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><?= $this->avatar->currentUserSmall('avatar-inline') ?><i class="fa fa-caret-down"></i></a>
    <ul>
        <li class="no-hover"><strong><?= $this->text->e($this->user->getFullname()) ?></strong></li>
        <li>
            <?= $this->url->icon('tachometer', t('My dashboard'), 'DashboardController', 'show', array('user_id' => $this->user->getId())) ?>
        </li>
        <li>
            <?= $this->url->icon('home', t('My profile'), 'UserViewController', 'show', array('user_id' => $this->user->getId())) ?>
        </li>
        <li>
            <?= $this->url->icon('folder', t('Projects management'), 'ProjectListController', 'show') ?>
        </li>
        <?php if ($this->user->hasAccess('UserListController', 'show')): ?>
            <li>
                <?= $this->url->icon('user', t('Users management'), 'UserListController', 'show') ?>
            </li>
            <li>
                <?= $this->url->icon('group', t('Groups management'), 'GroupListController', 'index') ?>
            </li>
            <li>
                <?= $this->url->icon('cubes', t('Plugins'), 'PluginController', 'show') ?>
            </li>
            <li>
                <?= $this->url->icon('cog', t('Settings'), 'ConfigController', 'index') ?>
            </li>
        <?php endif ?>

        <?= $this->hook->render('template:header:dropdown') ?>

        <li>
            <i class="fa fa-fw fa-life-ring" aria-hidden="true"></i>
            <?= $this->url->doc(t('Documentation')) ?>
        </li>
        <?php if (! DISABLE_LOGOUT): ?>
            <li>
                <?= $this->url->icon('sign-out', t('Logout'), 'AuthController', 'logout', [], true) ?>
            </li>
        <?php endif ?>
    </ul>
</div>
