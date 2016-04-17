<section id="main">
    <?= $this->projectHeader->render($project, 'Analytic', $this->app->getRouterAction()) ?>

    <?php if ($project['is_public']): ?>
    <div class="menu-inline pull-right">
        <ul>
            <li><?= $this->url->button('fa-rss-square', t('RSS feed'), 'feed', 'project', array('token' => $project['token']), false, '', '', true) ?></li>
            <li><?= $this->url->button('fa-calendar', t('iCal feed'), 'ical', 'project', array('token' => $project['token'])) ?></li>
        </ul>
    </div>
    <?php endif ?>

    <?= $this->render('event/events', array('events' => $events)) ?>
</section>
