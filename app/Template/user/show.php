<div class="page-header">
    <h2><?= t('Summary') ?></h2>
</div>
<ul class="listing">
    <li><?= t('Username:') ?> <strong><?= $this->e($user['username']) ?></strong></li>
    <li><?= t('Name:') ?> <strong><?= $this->e($user['name']) ?: t('None') ?></strong></li>
    <li><?= t('Email:') ?> <strong><?= $this->e($user['email']) ?: t('None') ?></strong></li>
    <li><?= t('Default project:') ?> <strong><?= (isset($user['default_project_id']) && isset($projects[$user['default_project_id']])) ? $this->e($projects[$user['default_project_id']]) : t('None') ?></strong></li>
    <li><?= t('Timezone:') ?> <strong><?= $this->inList($user['timezone'], $timezones) ?></strong></li>
    <li><?= t('Language:') ?> <strong><?= $this->inList($user['language'], $languages) ?></strong></li>
    <li><?= t('Notifications:') ?> <strong><?= $user['notifications_enabled'] == 1 ? t('Enabled') : t('Disabled') ?></strong></li>
    <li><?= t('Group:') ?> <strong><?= $user['is_admin'] ? t('Administrator') : t('Regular user') ?></strong></li>
    <li><?= t('Account type:') ?> <strong><?= $user['is_ldap_user'] ? t('Remote') : t('Local') ?></strong></li>
</ul>
