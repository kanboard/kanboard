<section id="main">
    <div class="page-header">
        <h2><?= t('Project "%s"', $project['name']) ?></h2>
        <ul>
            <li><a href="?controller=board&amp;action=show&amp;project_id=<?= $project['id'] ?>"><?= t('Back to the board') ?></a></li>
            <li><a href="?controller=project"><?= t('All projects') ?></a></li>
        </ul>
    </div>
    <section class="project-show" id="project-section">

        <?= Helper\template('project_sidebar', array('project' => $project)) ?>

        <div class="project-show-main">
            <?= $project_content_for_layout ?>
        </div>
    </section>
</section>