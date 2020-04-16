<section id="main">
    <div class="page-header">
        <ul>
            <?php if ($this->user->hasAccess('ProjectCreationController', 'create')): ?>
                <li>
                    <?= $this->modal->medium('plus', t('New project'), 'ProjectCreationController', 'create') ?>
                </li>
            <?php endif ?>
            <?php if ($this->app->config('disable_private_project', 0) == 0): ?>
                <li>
                    <?= $this->modal->medium('lock', t('New personal project'), 'ProjectCreationController', 'createPrivate') ?>
                </li>
            <?php endif ?>
            <li>
                <?= $this->url->icon('folder', t('Project management'), 'ProjectListController', 'show') ?>
            </li>
            <li>
                <?= $this->modal->medium('dashboard', t('My activity stream'), 'ActivityController', 'user') ?>
            </li>
            <?= $this->hook->render('template:dashboard:page-header:menu', array('user' => $user)) ?>
        </ul>
    </div>
    <section class="sidebar-container" id="dashboard">
        <?= $this->render($sidebar_template, array('user' => $user)) ?>
        <div class="sidebar-content">
            <?= $content_for_sublayout ?>
        </div>
    </section>
</section>
