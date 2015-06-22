<?= $this->asset->js('assets/js/vendor/d3.v3.min.js') ?>
<?= $this->asset->js('assets/js/vendor/c3.min.js') ?>

<section id="main">
    <div class="page-header">
        <ul>
            <li>
            <span class="dropdown">
                <span>
                    <i class="fa fa-caret-down"></i> <a href="#" class="dropdown-menu"><?= t('Actions') ?></a>
                    <ul>
                        <?= $this->render('project/dropdown', array('project' => $project)) ?>
                    </ul>
                </span>
            </span>
            </li>
            <li>
                <i class="fa fa-table fa-fw"></i>
                <?= $this->url->link(t('Back to the board'), 'board', 'show', array('project_id' => $project['id'])) ?>
            </li>
            <li>
                <i class="fa fa-folder fa-fw"></i>
                <?= $this->url->link(t('All projects'), 'project', 'index') ?>
            </li>
        </ul>
    </div>
    <section class="sidebar-container" id="analytic-section">

        <?= $this->render('analytic/sidebar', array('project' => $project)) ?>

        <div class="sidebar-content">
            <?= $content_for_sublayout ?>
        </div>
    </section>
</section>