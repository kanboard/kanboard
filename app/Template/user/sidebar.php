<div class="sidebar">
    <h2><?= t('Information') ?></h2>
    <ul>
        <li>
            <?= $this->url->link(t('Summary'), 'user', 'show', array('user_id' => $user['id'])) ?>
        </li>
        <?php if ($this->user->isAdmin()): ?>
            <li>
                <?= $this->url->link(t('User dashboard'), 'app', 'dashboard', array('user_id' => $user['id'])) ?>
            </li>
            <li>
                <?= $this->url->link(t('User calendar'), 'user', 'calendar', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
        <?php if ($this->user->isAdmin() || $this->user->isCurrentUser($user['id'])): ?>
            <li>
                <?= $this->url->link(t('Time tracking'), 'user', 'timesheet', array('user_id' => $user['id'])) ?>
            </li>
            <li>
                <?= $this->url->link(t('Last logins'), 'user', 'last', array('user_id' => $user['id'])) ?>
            </li>
            <li>
                <?= $this->url->link(t('Persistent connections'), 'user', 'sessions', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
    </ul>

    <h2><?= t('Actions') ?></h2>
    <ul>
        <?php if ($this->user->isAdmin() || $this->user->isCurrentUser($user['id'])): ?>
            <li>
                <?= $this->url->link(t('Edit profile'), 'user', 'edit', array('user_id' => $user['id'])) ?>
            </li>

            <?php if ($user['is_ldap_user'] == 0): ?>
                <li>
                    <?= $this->url->link(t('Change password'), 'user', 'password', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>

            <?php if ($this->user->isCurrentUser($user['id'])): ?>
                <li>
                    <?= $this->url->link(t('Two factor authentication'), 'twofactor', 'index', array('user_id' => $user['id'])) ?>
                </li>
            <?php elseif ($this->user->isAdmin() && $user['twofactor_activated'] == 1): ?>
                <li>
                    <?= $this->url->link(t('Two factor authentication'), 'twofactor', 'disable', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>

            <li>
                <?= $this->url->link(t('Public access'), 'user', 'share', array('user_id' => $user['id'])) ?>
            </li>
            <li>
                <?= $this->url->link(t('Email notifications'), 'user', 'notifications', array('user_id' => $user['id'])) ?>
            </li>
            <li>
                <?= $this->url->link(t('External accounts'), 'user', 'external', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>

        <?php if ($this->user->isAdmin()): ?>
            <li>
                <?= $this->url->link(t('Hourly rates'), 'hourlyrate', 'index', array('user_id' => $user['id'])) ?>
            </li>
            <li>
                <?= $this->url->link(t('Manage timetable'), 'timetable', 'index', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>

        <?php if ($this->user->isAdmin() && ! $this->user->isCurrentUser($user['id'])): ?>
            <li>
                <?= $this->url->link(t('Remove'), 'user', 'remove', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
    </ul>
</div>