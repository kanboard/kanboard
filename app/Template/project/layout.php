<section id="main">
    <div class="page-header">
        <ul>
            <li><i class="fa fa-table fa-fw"></i><?= $this->a(t('Back to the board'), 'board', 'show', array('project_id' => $project['id'])) ?></li>
            <li><i class="fa fa-folder fa-fw"></i><?= $this->a(t('All projects'), 'project', 'index') ?></li>
        </ul>
    </div>
    <section class="sidebar-container" id="project-section">

        <?= $this->render('project/sidebar', array('project' => $project)) ?>

        <div class="sidebar-content">
            <?= $project_content_for_layout ?>
        </div>
    </section>
</section>