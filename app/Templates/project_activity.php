<section id="main">
    <div class="page-header">
        <h2><?= t('%s\'s activity', $project['name']) ?></h2>
        <ul>
            <li><?= Helper\a(t('Back to the board'), 'board', 'show', array('project_id' => $project['id'])) ?></li>
            <li><?= Helper\a(t('Search'), 'project', 'search', array('project_id' => $project['id'])) ?></li>
            <li><?= Helper\a(t('Completed tasks'), 'project', 'tasks', array('project_id' => $project['id'])) ?></li>
            <li><?= Helper\a(t('List of projects'), 'project', 'index') ?></li>
        </ul>
    </div>
    <section>
        <?php if ($project['is_public']): ?>
            <p class="pull-right"><i class="fa fa-rss-square"></i> <?= Helper\a(t('RSS feed'), 'project', 'feed', array('token' => $project['token'])) ?></p>
        <?php endif ?>

        <?= Helper\template('project_events', array('events' => $events)) ?>
    </section>
</section>