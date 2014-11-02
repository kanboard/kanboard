<section id="main">
    <div class="page-header">
        <?php if (Helper\is_admin()): ?>
        <ul>
            <li><i class="fa fa-plus fa-fw"></i><?= Helper\a(t('New user'), 'user', 'create') ?></li>
        </ul>
        <?php endif ?>
    </div>
    <section>
    <?php if (empty($users)): ?>
        <p class="alert"><?= t('No user') ?></p>
    <?php else: ?>
        <table>
            <tr>
                <th><?= Helper\order(t('Id'), 'id', $pagination) ?></th>
                <th><?= Helper\order(t('Username'), 'username', $pagination) ?></th>
                <th><?= Helper\order(t('Name'), 'name', $pagination) ?></th>
                <th><?= Helper\order(t('Email'), 'email', $pagination) ?></th>
                <th><?= Helper\order(t('Administrator'), 'is_admin', $pagination) ?></th>
                <th><?= Helper\order(t('Default project'), 'default_project_id', $pagination) ?></th>
                <th><?= Helper\order(t('Notifications'), 'notifications_enabled', $pagination) ?></th>
                <th><?= t('External accounts') ?></th>
                <th><?= Helper\order(t('Account type'), 'is_ldap_user', $pagination) ?></th>
            </tr>
            <?php foreach ($users as $user): ?>
            <tr>
                <td>
                    <?= Helper\a('#'.$user['id'], 'user', 'show', array('user_id' => $user['id'])) ?>
                </td>
                <td>
                    <?= Helper\a(Helper\escape($user['username']), 'user', 'show', array('user_id' => $user['id'])) ?>
                </td>
                <td>
                    <?= Helper\escape($user['name']) ?>
                </td>
                <td>
                    <a href="mailto:<?= Helper\escape($user['email']) ?>"><?= Helper\escape($user['email']) ?></a>
                </td>
                <td>
                    <?= $user['is_admin'] ? t('Yes') : t('No') ?>
                </td>
                <td>
                    <?= (isset($user['default_project_id']) && isset($projects[$user['default_project_id']])) ? Helper\escape($projects[$user['default_project_id']]) : t('None'); ?>
                </td>
                <td>
                    <?php if ($user['notifications_enabled'] == 1): ?>
                        <?= t('Enabled') ?>
                    <?php else: ?>
                        <?= t('Disabled') ?>
                    <?php endif ?>
                </td>
                <td>
                    <ul class="no-bullet">
                    <?php if ($user['google_id']): ?>
                        <li><i class="fa fa-google fa-fw"></i><?= t('Google account linked') ?></li>
                    <?php endif ?>
                    <?php if ($user['github_id']): ?>
                        <li><i class="fa fa-github fa-fw"></i><?= t('Github account linked') ?></li>
                    <?php endif ?>
                    </ul>
                </td>
                <td>
                    <?= $user['is_ldap_user'] ? t('Remote') : t('Local') ?>
                </td>
            </tr>
            <?php endforeach ?>
        </table>

        <?= Helper\paginate($pagination) ?>
    <?php endif ?>
    </section>
</section>
