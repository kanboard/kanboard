<div class="page-header">
    <h2><?= t('Summary') ?></h2>
</div>
<ul class="settings">
    <li><?= t('Username:') ?> <strong><?= Helper\escape($user['username']) ?></strong></li>
    <li><?= t('Name:') ?> <strong><?= Helper\escape($user['name']) ?></strong></li>
    <li><?= t('Email:') ?> <strong><?= Helper\escape($user['email']) ?></strong></li>
    <li><?= t('Default project:') ?> <strong><?= (isset($user['default_project_id']) && isset($projects[$user['default_project_id']])) ? Helper\escape($projects[$user['default_project_id']]) : t('None'); ?></strong></li>
    <li><?= t('Notifications:') ?> <strong><?= $user['notifications_enabled'] == 1 ? t('Enabled') : t('Disabled') ?></strong></li>
    <li><?= t('Group:') ?> <strong><?= $user['is_admin'] ? t('Administrator') : t('Regular user') ?></strong></li>
    <li><?= t('Account type:') ?> <strong><?= $user['is_ldap_user'] ? t('Remote') : t('Local') ?></strong></li>
</ul>
