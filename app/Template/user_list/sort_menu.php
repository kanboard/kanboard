<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><strong><?= t('Sort') ?> <i class="fa fa-caret-down"></i></strong></a>
    <ul>
        <li>
            <?= $paginator->order(t('User ID'), \Kanboard\Model\UserModel::TABLE.'.id') ?>
        </li>
        <li>
            <?= $paginator->order(t('Username'), \Kanboard\Model\UserModel::TABLE.'.username') ?>
        </li>
        <li>
            <?= $paginator->order(t('Name'), \Kanboard\Model\UserModel::TABLE.'.name') ?>
        </li>
        <li>
            <?= $paginator->order(t('Email'), \Kanboard\Model\UserModel::TABLE.'.email') ?>
        </li>
        <li>
            <?= $paginator->order(t('Account type'), \Kanboard\Model\UserModel::TABLE.'.is_ldap_user') ?>
        </li>
        <li>
            <?= $paginator->order(t('Role'), \Kanboard\Model\UserModel::TABLE.'.role') ?>
        </li>
        <li>
            <?= $paginator->order(t('Two Factor'), \Kanboard\Model\UserModel::TABLE.'.twofactor_activated') ?>
        </li>
        <li>
            <?= $paginator->order(t('Status'), \Kanboard\Model\UserModel::TABLE.'.is_active') ?>
        </li>
    </ul>
</div>
