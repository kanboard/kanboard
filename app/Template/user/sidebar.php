<div class="sidebar">
    <h2><?= t('Information') ?></h2>
    <ul>
        <?php if ($this->user->hasAccess('user', 'show')): ?>
            <li <?= $this->app->checkMenuSelection('user', 'show') ?>>
                <?= $this->url->link(t('Summary'), 'user', 'show', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
        <?php if ($this->user->isAdmin()): ?>
            <li>
                <?= $this->url->link(t('User dashboard'), 'app', 'index', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
        <?php if ($this->user->isAdmin() || $this->user->isCurrentUser($user['id'])): ?>
            <li <?= $this->app->checkMenuSelection('user', 'timesheet') ?>>
                <?= $this->url->link(t('Time tracking'), 'user', 'timesheet', array('user_id' => $user['id'])) ?>
            </li>
            <li <?= $this->app->checkMenuSelection('user', 'last') ?>>
                <?= $this->url->link(t('Last logins'), 'user', 'last', array('user_id' => $user['id'])) ?>
            </li>
            <li <?= $this->app->checkMenuSelection('user', 'sessions') ?>>
                <?= $this->url->link(t('Persistent connections'), 'user', 'sessions', array('user_id' => $user['id'])) ?>
            </li>
            <li <?= $this->app->checkMenuSelection('user', 'passwordReset') ?>>
                <?= $this->url->link(t('Password reset history'), 'user', 'passwordReset', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>

        <?= $this->hook->render('template:user:sidebar:information', array('user' => $user)) ?>
    </ul>

    <h2><?= t('Actions') ?></h2>
    <ul>
        <?php if ($this->user->isAdmin() || $this->user->isCurrentUser($user['id'])): ?>

            <?php if ($this->user->hasAccess('user', 'edit')): ?>
                <li <?= $this->app->checkMenuSelection('user', 'edit') ?>>
                    <?= $this->url->link(t('Edit profile'), 'user', 'edit', array('user_id' => $user['id'])) ?>
                </li>
                <li <?= $this->app->checkMenuSelection('AvatarFile') ?>>
                    <?= $this->url->link(t('Avatar'), 'AvatarFile', 'show', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>

            <?php if ($user['is_ldap_user'] == 0): ?>
                <li <?= $this->app->checkMenuSelection('user', 'password') ?>>
                    <?= $this->url->link(t('Change password'), 'user', 'password', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>

            <?php if ($this->user->isCurrentUser($user['id'])): ?>
                <li <?= $this->app->checkMenuSelection('twofactor', 'index') ?>>
                    <?= $this->url->link(t('Two factor authentication'), 'twofactor', 'index', array('user_id' => $user['id'])) ?>
                </li>
            <?php elseif ($this->user->hasAccess('twofactor', 'disable') && $user['twofactor_activated'] == 1): ?>
                <li <?= $this->app->checkMenuSelection('twofactor', 'disable') ?>>
                    <?= $this->url->link(t('Two factor authentication'), 'twofactor', 'disable', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>

            <li <?= $this->app->checkMenuSelection('user', 'share') ?>>
                <?= $this->url->link(t('Public access'), 'user', 'share', array('user_id' => $user['id'])) ?>
            </li>
            <li <?= $this->app->checkMenuSelection('user', 'notifications') ?>>
                <?= $this->url->link(t('Notifications'), 'user', 'notifications', array('user_id' => $user['id'])) ?>
            </li>
            <li <?= $this->app->checkMenuSelection('user', 'external') ?>>
                <?= $this->url->link(t('External accounts'), 'user', 'external', array('user_id' => $user['id'])) ?>
            </li>
            <li <?= $this->app->checkMenuSelection('user', 'integrations') ?>>
                <?= $this->url->link(t('Integrations'), 'user', 'integrations', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>

        <?php if ($this->user->hasAccess('user', 'authentication')): ?>
            <li <?= $this->app->checkMenuSelection('user', 'authentication') ?>>
                <?= $this->url->link(t('Edit Authentication'), 'user', 'authentication', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>

        <?= $this->hook->render('template:user:sidebar:actions', array('user' => $user)) ?>
    </ul>
</div>