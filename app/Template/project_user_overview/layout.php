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
                <?= $this->url->icon('folder', t('Projects list'), 'ProjectListController', 'show') ?>
            </li>
        </ul>
    </div>
    <section class="sidebar-container">
        <?= $this->render($sidebar_template, array('users' => $users, 'filter' => $filter)) ?>

        <div class="sidebar-content">
            <div class="page-header">
                <h2><?= $this->text->e($title) ?></h2>
            </div>
            <?= $content_for_sublayout ?>
        </div>
    </section>
</section>
