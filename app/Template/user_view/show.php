<div class="page-header">
    <h2><?= t('Summary') ?></h2>
</div>
<ul class="panel">
    <li><?= t('Login:') ?> <strong><?= $this->text->e($user['username']) ?></strong></li>
    <li><?= t('Full Name:') ?> <strong><?= $this->text->e($user['name']) ?: t('None') ?></strong></li>
    <li><?= t('Email:') ?> <strong><?= $this->text->e($user['email']) ?: t('None') ?></strong></li>
    <li><?= t('Status:') ?> <strong><?= $user['is_active'] ? t('Active') : t('Inactive') ?></strong></li>
</ul>

<div class="page-header">
    <h2><?= t('Security') ?></h2>
</div>
<ul class="panel">
    <li><?= t('Role:') ?> <strong><?= $this->user->getRoleName($user['role']) ?></strong></li>
    <li><?= t('Account type:') ?> <strong><?= $user['is_ldap_user'] ? t('Remote') : t('Local') ?></strong></li>
    <li><?= $user['twofactor_activated'] == 1 ? t('Two factor authentication enabled') :  t('Two factor authentication disabled') ?></li>
    <li><?= t('Number of failed login:') ?> <strong><?= $user['nb_failed_login'] ?></strong></li>
    <?php if ($user['lock_expiration_date'] != 0): ?>
        <li><?= t('Account locked until:') ?> <strong><?= $this->dt->datetime($user['lock_expiration_date']) ?></strong></li>
        <?php if ($this->user->isAdmin()): ?>
            <li>
                <?= $this->url->link(t('Unlock this user'), 'UserCredentialController', 'unlock', array('user_id' => $user['id']), true) ?>
            </li>
        <?php endif ?>
    <?php endif ?>
</ul>

<div class="page-header">
    <h2><?= t('Preferences') ?></h2>
</div>
<ul class="panel">
    <li><?= t('Timezone:') ?> <strong><?= $this->text->in($user['timezone'], $timezones) ?></strong></li>
    <li><?= t('Language:') ?> <strong><?= $this->text->in($user['language'], $languages) ?></strong></li>
    <li><?= t('Notifications:') ?> <strong><?= $user['notifications_enabled'] == 1 ? t('Enabled') : t('Disabled') ?></strong></li>
</ul>

<?php if (! empty($user['token'])): ?>
    <div class="page-header">
        <h2><?= t('Public access') ?></h2>
    </div>

    <div class="panel">
        <ul class="no-bullet">
            <li><strong><?= $this->url->icon('rss-square', t('RSS feed'), 'FeedController', 'user', array('token' => $user['token']), false, '', '', true) ?></strong></li>
            <li><strong><?= $this->url->icon('calendar', t('iCal feed'), 'ICalendarController', 'user', array('token' => $user['token']), false, '', '', true) ?></strong></li>
        </ul>
    </div>
<?php endif ?>
