<section id="main">
    <div class="page-header">
        <h2><?= t('Users') ?><span id="page-counter"> (<?= $nb_users ?>)</span></h2>
        <?php if (Helper\is_admin()): ?>
        <ul>
            <li><a href="?controller=user&amp;action=create"><?= t('New user') ?></a></li>
        </ul>
        <?php endif ?>
    </div>
    <section>
    <?php if (empty($users)): ?>
        <p class="alert"><?= t('No user') ?></p>
    <?php else: ?>
        <table>
            <tr>
                <th><?= t('Id') ?></th>
                <th><?= t('Username') ?></th>
                <th><?= t('Name') ?></th>
                <th><?= t('Email') ?></th>
                <th><?= t('Administrator') ?></th>
                <th><?= t('Default project') ?></th>
                <th><?= t('Notifications') ?></th>
                <th><?= t('External accounts') ?></th>
                <th><?= t('Account type') ?></th>
            </tr>
            <?php foreach ($users as $user): ?>
            <tr>
                <td>
                    <a href="?controller=user&amp;action=show&amp;user_id=<?= $user['id'] ?>">#<?= $user['id'] ?></a>
                </td>
                <td>
                    <a href="?controller=user&amp;action=show&amp;user_id=<?= $user['id'] ?>"><?= Helper\escape($user['username']) ?></a>
                </td>
                <td>
                    <?= Helper\escape($user['name']) ?>
                </td>
                <td>
                    <?= Helper\escape($user['email']) ?>
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
                        <li><i class="fa fa-google"></i> <?= t('Google account linked') ?></li>
                    <?php endif ?>
                    <?php if ($user['github_id']): ?>
                        <li><i class="fa fa-github"></i> <?= t('Github account linked') ?></li>
                    <?php endif ?>
                    </ul>
                </td>
                <td>
                    <?= $user['is_ldap_user'] ? t('Remote') : t('Local') ?>
                </td>
            </tr>
            <?php endforeach ?>
        </table>
    <?php endif ?>
    </section>
</section>
