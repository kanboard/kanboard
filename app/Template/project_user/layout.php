<section id="main">
    <div class="page-header">
        <ul>
            <li>
                <?= $this->url->button('fa-folder', t('Projects list'), 'project', 'index') ?>
            </li>
            <?php if ($this->user->hasAccess('gantt', 'projects')): ?>
                <li>
                    <?= $this->url->button('fa-sliders', t('Projects Gantt chart'), 'gantt', 'projects') ?>
                </li>
            <?php endif ?>
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
