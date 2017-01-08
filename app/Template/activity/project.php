<section id="main">
    <?= $this->projectHeader->render($project, 'ActivityController', $this->app->getRouterAction()) ?>

    <?php if ($project['is_public']): ?>
    <div class="menu-inline">
        <ul>
            <li><?= $this->url->icon('rss-square', t('RSS feed'), 'FeedController', 'project', array('token' => $project['token']), false, '', '', true) ?></li>
            <li><?= $this->url->icon('calendar', t('iCal feed'), 'ICalendarController', 'project', array('token' => $project['token'])) ?></li>
        </ul>
    </div>
    <?php endif ?>

    <?= $this->render('event/events', array('events' => $events)) ?>
</section>
