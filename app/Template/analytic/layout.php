<?= $this->js('assets/js/d3.v3.4.8.min.js') ?>
<?= $this->js('assets/js/dimple.v2.1.0.min.js') ?>

<section id="main">
    <div class="page-header">
        <ul>
            <li><i class="fa fa-table fa-fw"></i><?= $this->a(t('Back to the board'), 'board', 'show', array('project_id' => $project['id'])) ?></li>
        </ul>
    </div>
    <section class="sidebar-container" id="analytic-section">

        <?= $this->render('analytic/sidebar', array('project' => $project)) ?>

        <div class="sidebar-content">
            <?= $analytic_content_for_layout ?>
        </div>
    </section>
</section>