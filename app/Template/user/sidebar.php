<div class="sidebar">
    <h2><?= t('Information') ?></h2>
    <ul>
        <li <?= $this->app->getRouterController() === 'user' && $this->app->getRouterAction() === 'show' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Summary'), 'user', 'show', array('user_id' => $user['id'])) ?>
        </li>
        <?php if ($this->user->isAdmin()): ?>
            <li>
                <?= $this->url->link(t('User dashboard'), 'app', 'index', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
        <?php if ($this->user->isAdmin() || $this->user->isCurrentUser($user['id'])): ?>
            <li <?= $this->app->getRouterController() === 'user' && $this->app->getRouterAction() === 'timesheet' ? 'class="active"' : '' ?>>
                <?= $this->url->link(t('Time tracking'), 'user', 'timesheet', array('user_id' => $user['id'])) ?>
            </li>
            <li <?= $this->app->getRouterController() === 'user' && $this->app->getRouterAction() === 'last' ? 'class="active"' : '' ?>>
                <?= $this->url->link(t('Last logins'), 'user', 'last', array('user_id' => $user['id'])) ?>
            </li>
            <li <?= $this->app->getRouterController() === 'user' && $this->app->getRouterAction() === 'sessions' ? 'class="active"' : '' ?>>
                <?= $this->url->link(t('Persistent connections'), 'user', 'sessions', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>

        <?= $this->hook->render('template:user:sidebar:information') ?>
    </ul>

    <h2><?= t('Actions') ?></h2>
    <ul>
        <?php if ($this->user->isAdmin() || $this->user->isCurrentUser($user['id'])): ?>
            <li <?= $this->app->getRouterController() === 'user' && $this->app->getRouterAction() === 'edit' ? 'class="active"' : '' ?>>
                <?= $this->url->link(t('Edit profile'), 'user', 'edit', array('user_id' => $user['id'])) ?>
            </li>

            <?php if ($user['is_ldap_user'] == 0): ?>
                <li <?= $this->app->getRouterController() === 'user' && $this->app->getRouterAction() === 'password' ? 'class="active"' : '' ?>>
                    <?= $this->url->link(t('Change password'), 'user', 'password', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>

            <?php if ($this->user->isCurrentUser($user['id'])): ?>
                <li <?= $this->app->getRouterController() === 'twofactor' && $this->app->getRouterAction() === 'index' ? 'class="active"' : '' ?>>
                    <?= $this->url->link(t('Two factor authentication'), 'twofactor', 'index', array('user_id' => $user['id'])) ?>
                </li>
            <?php elseif ($this->user->isAdmin() && $user['twofactor_activated'] == 1): ?>
                <li <?= $this->app->getRouterController() === 'twofactor' && $this->app->getRouterAction() === 'disable' ? 'class="active"' : '' ?>>
                    <?= $this->url->link(t('Two factor authentication'), 'twofactor', 'disable', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>

            <li <?= $this->app->getRouterController() === 'user' && $this->app->getRouterAction() === 'share' ? 'class="active"' : '' ?>>
                <?= $this->url->link(t('Public access'), 'user', 'share', array('user_id' => $user['id'])) ?>
            </li>
            <li <?= $this->app->getRouterController() === 'user' && $this->app->getRouterAction() === 'notifications' ? 'class="active"' : '' ?>>
                <?= $this->url->link(t('Notifications'), 'user', 'notifications', array('user_id' => $user['id'])) ?>
            </li>
            <li <?= $this->app->getRouterController() === 'user' && $this->app->getRouterAction() === 'external' ? 'class="active"' : '' ?>>
                <?= $this->url->link(t('External accounts'), 'user', 'external', array('user_id' => $user['id'])) ?>
            </li>
            <li <?= $this->app->getRouterController() === 'user' && $this->app->getRouterAction() === 'integrations' ? 'class="active"' : '' ?>>
                <?= $this->url->link(t('Integrations'), 'user', 'integrations', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>

        <?php if ($this->user->isAdmin()): ?>
            <li <?= $this->app->getRouterController() === 'user' && $this->app->getRouterAction() === 'authentication' ? 'class="active"' : '' ?>>
                <?= $this->url->link(t('Edit Authentication'), 'user', 'authentication', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>

        <?= $this->hook->render('template:user:sidebar:actions', array('user' => $user)) ?>

        <?php if ($this->user->isAdmin() && ! $this->user->isCurrentUser($user['id'])): ?>
            <li <?= $this->app->getRouterController() === 'user' && $this->app->getRouterAction() === 'remove' ? 'class="active"' : '' ?>>
                <?= $this->url->link(t('Remove'), 'user', 'remove', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
    </ul>
    <div class="sidebar-collapse"><a href="#" title="<?= t('Hide sidebar') ?>"><i class="fa fa-chevron-left"></i></a></div>
    <div class="sidebar-expand" style="display: none"><a href="#" title="<?= t('Expand sidebar') ?>"><i class="fa fa-chevron-right"></i></a></div>
</div>