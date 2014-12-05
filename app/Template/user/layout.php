<section id="main">
    <div class="page-header">
        <?php if (Helper\is_admin()): ?>
        <ul>
            <li><i class="fa fa-user fa-fw"></i><?= Helper\a(t('All users'), 'user', 'index') ?></li>
            <li><i class="fa fa-plus fa-fw"></i><?= Helper\a(t('New user'), 'user', 'create') ?></li>
        </ul>
        <?php endif ?>
    </div>
    <section class="sidebar-container" id="user-section">

        <?= Helper\template('user/sidebar', array('user' => $user)) ?>

        <div class="sidebar-content">
            <?= $user_content_for_layout ?>
        </div>
    </section>
</section>