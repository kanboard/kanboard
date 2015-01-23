<section id="main">
    <div class="page-header">
        <?php if ($this->userSession->isAdmin()): ?>
        <ul>
            <li><i class="fa fa-plus fa-fw"></i><?= $this->a(t('New user'), 'user', 'create') ?></li>
        </ul>
        <?php endif ?>
    </div>
    <section>
    <?php if ($paginator->isEmpty()): ?>
        <p class="alert"><?= t('No user') ?></p>
    <?php else: ?>
        <table>
            <tr>
                <th><?= $paginator->order(t('Id'), 'id') ?></th>
                <th><?= $paginator->order(t('Username'), 'username') ?></th>
                <th><?= $paginator->order(t('Name'), 'name') ?></th>
                <th><?= $paginator->order(t('Email'), 'email') ?></th>
                <th><?= $paginator->order(t('Administrator'), 'is_admin') ?></th>
                <th><?= $paginator->order(t('Default project'), 'default_project_id') ?></th>
                <th><?= $paginator->order(t('Notifications'), 'notifications_enabled') ?></th>
                <th><?= t('External accounts') ?></th>
                <th><?= $paginator->order(t('Account type'), 'is_ldap_user') ?></th>
            </tr>
            <?php foreach ($paginator->getCollection() as $user): ?>
            <tr>
                <td>
                    <?= $this->a('#'.$user['id'], 'user', 'show', array('user_id' => $user['id'])) ?>
                </td>
                <td>
                    <?= $this->a($this->e($user['username']), 'user', 'show', array('user_id' => $user['id'])) ?>
                </td>
                <td>
                    <?= $this->e($user['name']) ?>
                </td>
                <td>
                    <a href="mailto:<?= $this->e($user['email']) ?>"><?= $this->e($user['email']) ?></a>
                </td>
                <td>
                    <?= $user['is_admin'] ? t('Yes') : t('No') ?>
                </td>
                <td>
                    <?= (isset($user['default_project_id']) && isset($projects[$user['default_project_id']])) ? $this->e($projects[$user['default_project_id']]) : t('None'); ?>
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

        <?= $paginator ?>
    <?php endif ?>
    </section>
</section>
