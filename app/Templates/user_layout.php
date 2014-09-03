<section id="main">
    <div class="page-header">
        <h2><?= Helper\escape($user['name'] ?: $user['username']).' (#'.$user['id'].')' ?></h2>
        <ul>
            <li><a href="?controller=user&amp;action=index"><?= t('All users') ?></a></li>
            <?php if (Helper\is_admin()): ?>
                <li><a href="?controller=user&amp;action=create"><?= t('New user') ?></a></li>
            <?php endif ?>
        </ul>
    </div>
    <section class="user-show" id="user-section">

        <?= Helper\template('user_sidebar', array('user' => $user)) ?>

        <div class="user-show-main">
            <?= $user_content_for_layout ?>
        </div>
    </section>
</section>