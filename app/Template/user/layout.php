<section id="main">
    <div class="page-header">
        <?php if ($this->user->hasAccess('user', 'create')): ?>
        <ul class="btn-group">
            <li><?= $this->url->buttonLink('<fa-user>' . t('All users'), 'user', 'index') ?></li>
            <li><?= $this->url->buttonLink('<fa-plus>' . t('New local user'), 'user', 'create') ?></li>
            <li><?= $this->url->buttonLink('<fa-plus>' . t('New remote user'), 'user', 'create', array('remote' => 1)) ?></li>
        </ul>
        <?php endif ?>
    </div>
    <section class="sidebar-container" id="user-section">

        <?= $this->render('user/sidebar', array('user' => $user)) ?>

        <div class="sidebar-content">
            <?= $content_for_sublayout ?>
        </div>
    </section>
</section>
