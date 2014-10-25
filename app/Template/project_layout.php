<section id="main">
    <div class="page-header">
        <h2><?= t('Project "%s"', $project['name']) ?> (#<?= $project['id'] ?>)</h2>
        <ul>
            <li><?= Helper\a(t('Back to the board'), 'board', 'show', array('project_id' => $project['id'])) ?></li>
            <li><?= Helper\a(t('All projects'), 'project', 'index') ?></li>
        </ul>
    </div>
    <section class="project-show" id="project-section">

        <?= Helper\template('project_sidebar', array('project' => $project)) ?>

        <div class="project-show-main">
            <?= $project_content_for_layout ?>
        </div>
    </section>
</section>