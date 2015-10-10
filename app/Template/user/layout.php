<section id="main">
    <div class="page-header">
        <?php if ($this->user->isAdmin()): ?>
        <ul>
            <li><i class="fa fa-user fa-fw"></i><?= $this->url->link(t('All users'), 'user', 'index') ?></li>
            <li><i class="fa fa-plus fa-fw"></i><?= $this->url->link(t('New local user'), 'user', 'create') ?></li>
            <li><i class="fa fa-plus fa-fw"></i><?= $this->url->link(t('New remote user'), 'user', 'create', array('remote' => 1)) ?></li>
        </ul>
        <?php endif ?>
    </div>
    <section class="sidebar-container" id="user-section">

        <?= $this->render('user/sidebar', array('user' => $user)) ?>

        <div class="sidebar-content">
            <?= $user_content_for_layout ?>
        </div>
    </section>
</section>