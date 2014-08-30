<section id="main">
    <div class="page-header">
        <h2><?= t('Projects') ?><span id="page-counter"> (<?= $nb_projects ?>)</span></h2>
        <?php if (Helper\is_admin()): ?>
        <ul>
            <li><a href="?controller=project&amp;action=create"><?= t('New project') ?></a></li>
        </ul>
        <?php endif ?>
    </div>
    <section>
    <?php if (empty($active_projects) && empty($inactive_projects)): ?>
        <p class="alert"><?= t('No project') ?></p>
    <?php else: ?>

        <?php if (! empty($active_projects)): ?>
            <h3><?= t('Active projects') ?></h3>
            <ul class="project-listing">
                <?php foreach ($active_projects as $project): ?>
                    <li>
                        <a href="?controller=project&amp;action=show&amp;project_id=<?= $project['id'] ?>"><?= Helper\escape($project['name']) ?></a>
                    </li>
                <?php endforeach ?>
            </ul>
        <?php endif ?>

        <?php if (! empty($inactive_projects)): ?>
            <h3><?= t('Inactive projects') ?></h3>
            <ul class="project-listing">
                <?php foreach ($inactive_projects as $project): ?>
                    <li>
                        <a href="?controller=project&amp;action=show&amp;project_id=<?= $project['id'] ?>"><?= Helper\escape($project['name']) ?></a>
                    </li>
                <?php endforeach ?>
            </ul>
        <?php endif ?>

    <?php endif ?>
    </section>
</section>