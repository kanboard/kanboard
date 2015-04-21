<div class="sidebar">
    <h2><?= t('Information') ?></h2>
    <ul>
        <li>
            <?= $this->a(t('Summary'), 'user', 'show', array('user_id' => $user['id'])) ?>
        </li>
        <?php if ($this->userSession->isAdmin()): ?>
            <li>
                <?= $this->a(t('User dashboard'), 'app', 'dashboard', array('user_id' => $user['id'])) ?>
            </li>
            <li>
                <?= $this->a(t('User calendar'), 'user', 'calendar', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
        <?php if ($this->userSession->isAdmin() || $this->userSession->isCurrentUser($user['id'])): ?>
            <li>
                <?= $this->a(t('Time tracking'), 'user', 'timesheet', array('user_id' => $user['id'])) ?>
            </li>
            <li>
                <?= $this->a(t('Last logins'), 'user', 'last', array('user_id' => $user['id'])) ?>
            </li>
            <li>
                <?= $this->a(t('Persistent connections'), 'user', 'sessions', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
    </ul>

    <h2><?= t('Actions') ?></h2>
    <ul>
        <?php if ($this->userSession->isAdmin() || $this->userSession->isCurrentUser($user['id'])): ?>
            <li>
                <?= $this->a(t('Edit profile'), 'user', 'edit', array('user_id' => $user['id'])) ?>
            </li>

            <?php if ($user['is_ldap_user'] == 0): ?>
                <li>
                    <?= $this->a(t('Change password'), 'user', 'password', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>

            <?php if ($this->userSession->isCurrentUser($user['id'])): ?>
                <li>
                    <?= $this->a(t('Two factor authentication'), 'twofactor', 'index', array('user_id' => $user['id'])) ?>
                </li>
            <?php elseif ($this->userSession->isAdmin() && $user['twofactor_activated'] == 1): ?>
                <li>
                    <?= $this->a(t('Two factor authentication'), 'twofactor', 'disable', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>

            <li>
                <?= $this->a(t('Email notifications'), 'user', 'notifications', array('user_id' => $user['id'])) ?>
            </li>
            <li>
                <?= $this->a(t('External accounts'), 'user', 'external', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>

        <?php if ($this->userSession->isAdmin()): ?>
            <li>
                <?= $this->a(t('Hourly rates'), 'hourlyrate', 'index', array('user_id' => $user['id'])) ?>
            </li>
            <li>
                <?= $this->a(t('Manage timetable'), 'timetable', 'index', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>

        <?php if ($this->userSession->isAdmin() && ! $this->userSession->isCurrentUser($user['id'])): ?>
            <li>
                <?= $this->a(t('Remove'), 'user', 'remove', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
    </ul>
</div>