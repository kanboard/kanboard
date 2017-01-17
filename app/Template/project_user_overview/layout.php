<section id="main">
    <div class="page-header">
        <ul>
            <li>
                <?= $this->url->icon('folder', t('Projects list'), 'ProjectListController', 'show') ?>
            </li>
            <?php if ($this->user->hasAccess('ProjectGanttController', 'show')): ?>
                <li>
                    <?= $this->url->icon('sliders', t('Projects Gantt chart'), 'ProjectGanttController', 'show') ?>
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
