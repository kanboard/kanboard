<section id="main">
    <div class="page-header">
        <?php if ($this->user->hasAccess('UserCreationController', 'show')): ?>
        <ul>
            <li>
                <?= $this->url->icon('user', t('All users'), 'UserListController', 'show') ?>
            </li>
            <li>
                <?= $this->modal->medium('plus', t('New user'), 'UserCreationController', 'show') ?>
            </li>
            <li>
                <?= $this->modal->medium('upload', t('Import'), 'UserImportController', 'show') ?>
            </li>
            <li>
                <?= $this->url->icon('users', t('View all groups'), 'GroupListController', 'index') ?>
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
