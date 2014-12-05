<div class="sidebar">
    <h2><?= t('Actions') ?></h2>
    <ul>
        <li>
            <?= Helper\a(t('Summary'), 'user', 'show', array('user_id' => $user['id'])) ?>
        </li>

        <?php if (Helper\is_admin() || Helper\is_current_user($user['id'])): ?>
            <li>
                <?= Helper\a(t('Edit profile'), 'user', 'edit', array('user_id' => $user['id'])) ?>
            </li>

            <?php if ($user['is_ldap_user'] == 0): ?>
                <li>
                    <?= Helper\a(t('Change password'), 'user', 'password', array('user_id' => $user['id'])) ?>
                </li>
            <?php endif ?>

            <li>
                <?= Helper\a(t('Email notifications'), 'user', 'notifications', array('user_id' => $user['id'])) ?>
            </li>
            <li>
                <?= Helper\a(t('External accounts'), 'user', 'external', array('user_id' => $user['id'])) ?>
            </li>
            <li>
                <?= Helper\a(t('Last logins'), 'user', 'last', array('user_id' => $user['id'])) ?>
            </li>
            <li>
                <?= Helper\a(t('Persistent connections'), 'user', 'sessions', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>

        <?php if (Helper\is_admin() && ! Helper\is_current_user($user['id'])): ?>
            <li>
                <?= Helper\a(t('Remove'), 'user', 'remove', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
    </ul>
</div>