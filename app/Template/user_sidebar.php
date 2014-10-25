<div class="project-show-sidebar">
    <h2><?= t('Actions') ?></h2>
    <div class="user-show-actions">
        <ul>
            <li>
                <a href="?controller=user&amp;action=show&amp;user_id=<?= $user['id'] ?>"><?= t('Summary') ?></a>
            </li>

            <?php if (Helper\is_admin() || Helper\is_current_user($user['id'])): ?>
            <li>
                <a href="?controller=user&amp;action=edit&amp;user_id=<?= $user['id'] ?>"><?= t('Edit profile') ?></a>
            </li>

            <?php if ($user['is_ldap_user'] == 0): ?>
            <li>
                <a href="?controller=user&amp;action=password&amp;user_id=<?= $user['id'] ?>"><?= t('Change password') ?></a>
            </li>
            <?php endif ?>

            <li>
                <a href="?controller=user&amp;action=notifications&amp;user_id=<?= $user['id'] ?>"><?= t('Email notifications') ?></a>
            </li>
            <li>
                <a href="?controller=user&amp;action=external&amp;user_id=<?= $user['id'] ?>"><?= t('External accounts') ?></a>
            </li>
            <li>
                <a href="?controller=user&amp;action=last&amp;user_id=<?= $user['id'] ?>"><?= t('Last logins') ?></a>
            </li>
            <li>
                <a href="?controller=user&amp;action=sessions&amp;user_id=<?= $user['id'] ?>"><?= t('Persistent connections') ?></a>
            </li>
            <?php endif ?>

            <?php if (Helper\is_admin()): ?>
            <li>
                <a href="?controller=user&amp;action=remove&amp;user_id=<?= $user['id'] ?>"><?= t('Remove') ?></a>
            </li>
            <?php endif ?>

        </ul>
    </div>
</div>