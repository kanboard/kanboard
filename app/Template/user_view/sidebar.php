<div class="sidebar">
    <div class="sidebar-title">
        <h2><?= t('Information') ?></h2>
    </div>
    <ul>
        <?php if ($this->user->hasAccess('UserViewController', 'show')): ?>
            <li <?= $this->app->checkMenuSelection('UserViewController', 'show') ?>>
                <?= $this->url->link(t('Summary'), 'UserViewController', 'show', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
        <?php if ($this->user->isAdmin()): ?>
            <li>
                <?= $this->url->link(t('User dashboard'), 'DashboardController', 'show', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
        <?php if ($this->user->isAdmin() || $this->user->isCurrentUser($user['id'])): ?>
            <?php if ($this->user->hasAccess('UserViewController', 'timesheet')): ?>
                <li <?= $this->app->checkMenuSelection('UserViewController', 'timesheet') ?>>
                    <?= $this->url->link(t('Time tracking'), 'UserViewController', 'timesheet', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>
            <?php if ($this->user->hasAccess('UserViewController', 'lastLogin')): ?>
                <li <?= $this->app->checkMenuSelection('UserViewController', 'lastLogin') ?>>
                    <?= $this->url->link(t('Last logins'), 'UserViewController', 'lastLogin', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>
            <?php if ($this->user->hasAccess('UserViewController', 'sessions')): ?>
                <li <?= $this->app->checkMenuSelection('UserViewController', 'sessions') ?>>
                    <?= $this->url->link(t('Persistent connections'), 'UserViewController', 'sessions', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>
            <?php if ($this->user->hasAccess('UserViewController', 'passwordReset')): ?>
                <li <?= $this->app->checkMenuSelection('UserViewController', 'passwordReset') ?>>
                    <?= $this->url->link(t('Password reset history'), 'UserViewController', 'passwordReset', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>
        <?php endif ?>

        <?= $this->hook->render('template:user:sidebar:information', array('user' => $user)) ?>
    </ul>

    <div class="sidebar-title">
        <h2><?= t('Actions') ?></h2>
    </div>
    <ul>
        <?php if ($this->user->isAdmin() || $this->user->isCurrentUser($user['id'])): ?>

            <?php if ($this->user->hasAccess('UserModificationController', 'show')): ?>
                <li <?= $this->app->checkMenuSelection('UserModificationController', 'show') ?>>
                    <?= $this->url->link(t('Edit profile'), 'UserModificationController', 'show', array('user_id' => $user['id'])) ?>
                </li>
                <li <?= $this->app->checkMenuSelection('AvatarFileController') ?>>
                    <?= $this->url->link(t('Avatar'), 'AvatarFileController', 'show', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>

            <?php if ($user['is_ldap_user'] == 0 && $this->user->hasAccess('UserCredentialController', 'changePassword')): ?>
                <li <?= $this->app->checkMenuSelection('UserCredentialController', 'changePassword') ?>>
                    <?= $this->url->link(t('Change password'), 'UserCredentialController', 'changePassword', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>

            <?php if ($this->user->isCurrentUser($user['id']) && $this->user->hasAccess('TwoFactorController', 'index')): ?>
                <li <?= $this->app->checkMenuSelection('TwoFactorController', 'index') ?>>
                    <?= $this->url->link(t('Two factor authentication'), 'TwoFactorController', 'index', array('user_id' => $user['id'])) ?>
                </li>
            <?php elseif ($this->user->hasAccess('TwoFactorController', 'disable') && $user['twofactor_activated'] == 1): ?>
                <li <?= $this->app->checkMenuSelection('TwoFactorController', 'disable') ?>>
                    <?= $this->url->link(t('Two factor authentication'), 'TwoFactorController', 'disable', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>

            <?php if ($this->user->hasAccess('UserViewController', 'share')): ?>
                <li <?= $this->app->checkMenuSelection('UserViewController', 'share') ?>>
                    <?= $this->url->link(t('Public access'), 'UserViewController', 'share', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>
            <?php if ($this->user->hasAccess('UserViewController', 'notifications')): ?>
                <li <?= $this->app->checkMenuSelection('UserViewController', 'notifications') ?>>
                    <?= $this->url->link(t('Notifications'), 'UserViewController', 'notifications', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>
            <?php if ($this->user->hasAccess('UserViewController', 'external')): ?>
                <li <?= $this->app->checkMenuSelection('UserViewController', 'external') ?>>
                    <?= $this->url->link(t('External accounts'), 'UserViewController', 'external', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>
            <?php if ($this->user->hasAccess('UserViewController', 'integrations')): ?>
                <li <?= $this->app->checkMenuSelection('UserViewController', 'integrations') ?>>
                    <?= $this->url->link(t('Integrations'), 'UserViewController', 'integrations', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>
            <?php if ($this->user->hasAccess('UserApiAccessController', 'show')): ?>
                <li <?= $this->app->checkMenuSelection('UserApiAccessController', 'show') ?>>
                    <?= $this->url->link(t('API'), 'UserApiAccessController', 'show', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>
        <?php endif ?>

        <?php if ($this->user->hasAccess('UserCredentialController', 'changeAuthentication')): ?>
            <li <?= $this->app->checkMenuSelection('UserCredentialController', 'changeAuthentication') ?>>
                <?= $this->url->link(t('Edit Authentication'), 'UserCredentialController', 'changeAuthentication', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>

        <?= $this->hook->render('template:user:sidebar:actions', array('user' => $user)) ?>
    </ul>
</div>
