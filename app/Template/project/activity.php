<section id="main">
    <div class="page-header">
        <ul>
            <li><i class="fa fa-table fa-fw"></i><?= Helper\a(t('Back to the board'), 'board', 'show', array('project_id' => $project['id'])) ?></li>
            <li><i class="fa fa-search fa-fw"></i><?= Helper\a(t('Search'), 'project', 'search', array('project_id' => $project['id'])) ?></li>
            <li><i class="fa fa-check-square-o fa-fw"></i><?= Helper\a(t('Completed tasks'), 'project', 'tasks', array('project_id' => $project['id'])) ?></li>
        </ul>
    </div>
    <section>
        <?php if ($project['is_public']): ?>
            <p class="pull-right"><i class="fa fa-rss-square"></i> <?= Helper\a(t('RSS feed'), 'project', 'feed', array('token' => $project['token'])) ?></p>
        <?php endif ?>

        <?= Helper\template('project/events', array('events' => $events)) ?>
    </section>
</section>