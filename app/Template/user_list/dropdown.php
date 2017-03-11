<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><strong><?= '#'.$user['id'] ?> <i class="fa fa-caret-down"></i></strong></a>
    <ul>
        <li>
            <?= $this->url->icon('user', t('View profile'), 'UserViewController', 'show', array('user_id' => $user['id'])) ?>
        </li>
        <?php if ($user['is_active'] == 1 && $this->user->hasAccess('UserModificationController', 'show')): ?>
            <li>
                <?= $this->modal->medium('edit', t('Edit'), 'UserModificationController', 'show', array('user_id' => $user['id'])) ?>
            </li>
            <li>
                <?= $this->modal->medium('smile-o', t('Avatar'), 'AvatarFileController', 'show', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
        <?php if ($user['is_ldap_user'] == 0 && $this->user->hasAccess('UserCredentialController', 'changePassword')): ?>
            <li>
                <?= $this->modal->medium('key', t('Change password'), 'UserCredentialController', 'changePassword', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
        <?php if ($this->user->isCurrentUser($user['id']) && $this->user->hasAccess('TwoFactorController', 'index')): ?>
            <li>
                <?= $this->modal->medium('shield', t('Two factor authentication'), 'TwoFactorController', 'index', array('user_id' => $user['id'])) ?>
            </li>
        <?php elseif ($this->user->hasAccess('TwoFactorController', 'disable') && $user['twofactor_activated'] == 1): ?>
            <li>
                <?= $this->modal->medium('shield', t('Two factor authentication'), 'TwoFactorController', 'disable', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
        <?php if ($this->user->hasAccess('UserViewController', 'share')): ?>
            <li>
                <?= $this->modal->medium('share-alt', t('Public access'), 'UserViewController', 'share', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
        <?php if ($this->user->hasAccess('UserViewController', 'notifications')): ?>
            <li>
                <?= $this->modal->medium('bell-o', t('Notifications'), 'UserViewController', 'notifications', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
        <?php if ($this->user->hasAccess('UserViewController', 'external')): ?>
            <li>
                <?= $this->modal->medium('user-circle-o', t('External accounts'), 'UserViewController', 'external', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
        <?php if ($this->user->hasAccess('UserViewController', 'integrations')): ?>
            <li>
                <?= $this->modal->medium('rocket', t('Integrations'), 'UserViewController', 'integrations', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
        <?php if ($this->user->hasAccess('UserApiAccessController', 'show')): ?>
            <li>
                <?= $this->modal->medium('cloud', t('API Access'), 'UserApiAccessController', 'show', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>

        <?php if ($this->user->isAdmin()): ?>
            <li>
                <?= $this->url->icon('dashboard', t('User dashboard'), 'DashboardController', 'show', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>

        <?php if ($this->user->isAdmin() || $this->user->isCurrentUser($user['id'])): ?>
            <?php if ($this->user->hasAccess('UserViewController', 'timesheet')): ?>
                <li>
                    <?= $this->modal->medium('clock-o',t('Time tracking'), 'UserViewController', 'timesheet', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>
            <?php if ($this->user->hasAccess('UserViewController', 'lastLogin')): ?>
                <li>
                    <?= $this->modal->medium('id-badge', t('Last logins'), 'UserViewController', 'lastLogin', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>
            <?php if ($this->user->hasAccess('UserViewController', 'sessions')): ?>
                <li>
                    <?= $this->modal->medium('database', t('Persistent connections'), 'UserViewController', 'sessions', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>
            <?php if ($this->user->hasAccess('UserViewController', 'passwordReset')): ?>
                <li>
                    <?= $this->modal->medium('legal', t('Password reset history'), 'UserViewController', 'passwordReset', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>
        <?php endif ?>

        <?php if ($user['is_active'] == 1 && $this->user->hasAccess('UserStatusController', 'disable') && ! $this->user->isCurrentUser($user['id'])): ?>
            <li>
                <?= $this->modal->confirm('times', t('Disable'), 'UserStatusController', 'confirmDisable', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
        <?php if ($user['is_active'] == 0 && $this->user->hasAccess('UserStatusController', 'enable') && ! $this->user->isCurrentUser($user['id'])): ?>
            <li>
                <?= $this->modal->confirm('check-square-o', t('Enable'), 'UserStatusController', 'confirmEnable', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
        <?php if ($this->user->hasAccess('UserStatusController', 'remove') && ! $this->user->isCurrentUser($user['id'])): ?>
            <li>
                <?= $this->modal->confirm('trash-o', t('Remove'), 'UserStatusController', 'confirmRemove', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
    </ul>
</div>
