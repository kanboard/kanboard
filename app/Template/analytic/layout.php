<?= $this->js('assets/js/vendor/d3.v3.4.8.min.js') ?>
<?= $this->js('assets/js/vendor/dimple.v2.1.2.min.js') ?>

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