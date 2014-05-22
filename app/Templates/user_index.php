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
                <th><?= t('Username') ?></th>
                <th><?= t('Name') ?></th>
                <th><?= t('Email') ?></th>
                <th><?= t('Administrator') ?></th>
                <th><?= t('Default Project') ?></th>
                <th><?= t('Actions') ?></th>
            </tr>
            <?php foreach ($users as $user): ?>
            <tr>
                <td>
                    <span title="user_id=<?= $user['id'] ?>"><?= Helper\escape($user['username']) ?></span>
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
                    <?php if (Helper\is_admin() || Helper\is_current_user($user['id'])): ?>
                        <a href="?controller=user&amp;action=edit&amp;user_id=<?= $user['id'] ?>"><?= t('edit') ?></a>
                    <?php endif ?>
                    <?php if (Helper\is_admin()): ?>
                        <?php if (count($users) > 1): ?>
                            <?= t('or') ?>
                            <a href="?controller=user&amp;action=confirm&amp;user_id=<?= $user['id'] ?>"><?= t('remove') ?></a>
                        <?php endif ?>
                    <?php endif ?>
                </td>
            </tr>
            <?php endforeach ?>
        </table>
    <?php endif ?>
    </section>
</section>
