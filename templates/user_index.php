<section id="main">
    <div class="page-header">
        <h2><?= t('Users') ?><span id="page-counter"> (<?= $nb_users ?>)</span></h2>
        <?php if ($_SESSION['user']['is_admin'] == 1): ?>
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
                <th><?= t('Administrator') ?></th>
                <th><?= t('Default Project') ?></th>
                <th><?= t('Actions') ?></th>
            </tr>
            <?php foreach ($users as $user): ?>
            <tr>
                <td>
                    <?= Helper\escape($user['username']) ?>
                </td>
                <td>
                    <?= $user['is_admin'] ? t('Yes') : t('No') ?>
                </td>
                <td>
                    <?= $projects[$user['default_project_id']] ?>
                </td>
                <td>
                    <?php if ($_SESSION['user']['is_admin'] == 1 || $_SESSION['user']['id'] == $user['id']): ?>
                        <a href="?controller=user&amp;action=edit&amp;user_id=<?= $user['id'] ?>"><?= t('edit') ?></a>
                    <?php endif ?>
                    <?php if ($_SESSION['user']['is_admin'] == 1): ?>
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