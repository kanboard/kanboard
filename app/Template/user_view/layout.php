<section id="main">
    <div class="page-header">
        <?php if ($this->user->hasAccess('UserCreationController', 'show')): ?>
        <ul>
            <li>
                <i class="fa fa-user fa-fw"></i><?= $this->url->link(t('All users'), 'UserListController', 'show') ?>
            </li>
            <li>
                <?= $this->modal->medium('plus', t('New local user'), 'UserCreationController', 'show') ?>
            </li>
            <li>
                <?= $this->modal->medium('plus', t('New remote user'), 'UserCreationController', 'show', array('remote' => 1)) ?>
            </li>
            <li>
                <?= $this->modal->medium('upload', t('Import'), 'UserImportController', 'show') ?>
            </li>
            <li>
                <i class="fa fa-users fa-fw"></i><?= $this->url->link(t('View all groups'), 'GroupListController', 'index') ?>
            </li>
        </ul>
        <?php endif ?>
    </div>
    <section class="sidebar-container" id="user-section">
        <?= $this->render('user_view/sidebar', array('user' => $user)) ?>
        <div class="sidebar-content">
            <?= $content_for_sublayout ?>
        </div>
    </section>
</section>
