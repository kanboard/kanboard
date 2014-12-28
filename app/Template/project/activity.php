<section id="main">
    <div class="page-header">
        <ul>
            <li><i class="fa fa-table fa-fw"></i><?= $this->a(t('Back to the board'), 'board', 'show', array('project_id' => $project['id'])) ?></li>
            <li><i class="fa fa-search fa-fw"></i><?= $this->a(t('Search'), 'project', 'search', array('project_id' => $project['id'])) ?></li>
            <li><i class="fa fa-check-square-o fa-fw"></i><?= $this->a(t('Completed tasks'), 'project', 'tasks', array('project_id' => $project['id'])) ?></li>
            <?php if ($project['is_public']): ?>
                <li><i class="fa fa-rss-square fa-fw"></i><?= $this->a(t('RSS feed'), 'project', 'feed', array('token' => $project['token']), false, '', '', true) ?></li>
            <?php endif ?>
        </ul>
    </div>
    <section>
        <?= $this->render('project/events', array('events' => $events)) ?>
    </section>
</section>