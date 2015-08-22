<section id="main">
    <div class="page-header">
        <ul>
            <?php if ($this->user->isProjectAdmin() || $this->user->isAdmin()): ?>
                <li><i class="fa fa-plus fa-fw"></i><?= $this->url->link(t('New project'), 'project', 'create') ?></li>
            <?php endif ?>
            <li>
                <i class="fa fa-lock fa-fw"></i>
                <?= $this->url->link(t('New private project'), 'project', 'create', array('private' => 1)) ?>
            </li>
            <li>
                <i class="fa fa-folder fa-fw"></i>
                <?= $this->url->link(t('Projects list'), 'project', 'index') ?>
            </li>
            <?php if ($this->user->isProjectAdmin() || $this->user->isAdmin()): ?>
                <li>
                    <i class="fa fa-sliders fa-fw"></i>
                    <?= $this->url->link(t('Projects Gantt chart'), 'gantt', 'projects') ?>
                </li>
            <?php endif ?>
        </ul>
    </div>
    <section class="sidebar-container">

        <?= $this->render('project_user/sidebar', array('users' => $users, 'filter' => $filter)) ?>

        <div class="sidebar-content">
            <div class="page-header">
                <h2><?= $this->e($title) ?></h2>
            </div>
            <?= $content_for_sublayout ?>
        </div>
    </section>
</section>