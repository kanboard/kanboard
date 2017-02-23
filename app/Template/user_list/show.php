<section id="main">
    <div class="page-header">
        <?php if ($this->user->hasAccess('UserCreationController', 'show')): ?>
        <ul>
            <li>
                <?= $this->modal->medium('plus', t('New user'), 'UserCreationController', 'show') ?>
            </li>
            <li>
                <?= $this->modal->medium('paper-plane', t('Invite people'), 'UserInviteController', 'show') ?>
            </li>
            <li>
                <?= $this->modal->medium('upload', t('Import'), 'UserImportController', 'show') ?>
            </li>
            <li>
                <?= $this->url->icon('users', t('View all groups'), 'GroupListController', 'index') ?>
            </li>
        </ul>
        <?php endif ?>
    </div>
    <?php if ($paginator->isEmpty()): ?>
        <p class="alert"><?= t('No user') ?></p>
    <?php else: ?>
        <table class="table-scrolling table-striped table-hover">
            <tr>
                <th class="column-5"><?= $paginator->order(t('Id'), 'id') ?></th>
                <th class="column-18"><?= $paginator->order(t('Username'), 'username') ?></th>
                <th class="column-18"><?= $paginator->order(t('Name'), 'name') ?></th>
                <th class="column-15"><?= $paginator->order(t('Email'), 'email') ?></th>
                <th class="column-15"><?= $paginator->order(t('Role'), 'role') ?></th>
                <th class="column-12"><?= $paginator->order(t('Two Factor'), 'twofactor_activated') ?></th>
                <th class="column-10"><?= $paginator->order(t('Account type'), 'is_ldap_user') ?></th>
                <th class="column-12"><?= $paginator->order(t('Status'), 'is_active') ?></th>
            </tr>
            <?php foreach ($paginator->getCollection() as $user): ?>
            <tr>
                <td>
                    <?= $this->render('user_list/dropdown', array('user' => $user)) ?>
                </td>
                <td>
                    <?= $this->url->link($this->text->e($user['username']), 'UserViewController', 'show', array('user_id' => $user['id'])) ?>
                </td>
                <td>
                    <?= $this->text->e($user['name']) ?>
                </td>
                <td>
                    <a href="mailto:<?= $this->text->e($user['email']) ?>"><?= $this->text->e($user['email']) ?></a>
                </td>
                <td>
                    <?= $this->user->getRoleName($user['role']) ?>
                </td>
                <td>
                    <?= $user['twofactor_activated'] ? t('Yes') : t('No') ?>
                </td>
                <td>
                    <?= $user['is_ldap_user'] ? t('Remote') : t('Local') ?>
                </td>
                <td>
                    <?php if ($user['is_active'] == 1): ?>
                        <?= t('Active') ?>
                    <?php else: ?>
                        <?= t('Inactive') ?>
                    <?php endif ?>
                </td>
            </tr>
            <?php endforeach ?>
        </table>

        <?= $paginator ?>
    <?php endif ?>
</section>
