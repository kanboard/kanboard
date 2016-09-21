<section id="main">
    <?= $this->projectHeader->render($project, 'AnalyticController', $this->app->getRouterAction()) ?>

    <?php if ($project['is_public']): ?>
    <div class="menu-inline pull-right">
        <ul>
            <li><?= $this->url->link('<i class="fa fa-rss-square fa-fw"></i>' . t('RSS feed'), 'FeedController', 'project', array('token' => $project['token']), false, '', '', true) ?></li>
            <li><?= $this->url->link('<i class="fa fa-calendar fa-fw"></i>' . t('iCal feed'), 'ICalendarController', 'project', array('token' => $project['token'])) ?></li>
        </ul>
    </div>
    <?php endif ?>

    <?= $this->render('event/events', array('events' => $events)) ?>
</section>
