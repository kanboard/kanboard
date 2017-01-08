<div class="page-header">
    <h2><?= t('%s\'s activity', $project['name']) ?></h2>

    <?php if ($project['is_public']): ?>
        <ul>
            <li><?= $this->url->icon('rss-square', t('RSS feed'), 'FeedController', 'project', array('token' => $project['token']), false, '', '', true) ?></li>
            <li><?= $this->url->icon('calendar', t('iCal feed'), 'ICalendarController', 'project', array('token' => $project['token'])) ?></li>
        </ul>
    <?php endif ?>
</div>

<?= $this->render('event/events', array('events' => $events)) ?>
