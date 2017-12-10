<div class="page-header">
    <h2><?= t('Public access') ?></h2>
</div>


<?php if ($project['is_public']): ?>

    <div class="panel">
        <ul class="no-bullet">
            <li><strong><?= $this->url->icon('share-alt', t('Public link'), 'BoardViewController', 'readonly', array('token' => $project['token']), false, '', '', true) ?></strong></li>
            <li><strong><?= $this->url->icon('rss-square', t('RSS feed'), 'FeedController', 'project', array('token' => $project['token']), false, '', '', true) ?></strong></li>
            <li><strong><?= $this->url->icon('calendar', t('iCal feed'), 'ICalendarController', 'project', array('token' => $project['token']), false, '', '', true) ?></strong></li>
        </ul>
    </div>

    <?= $this->url->link(t('Disable public access'), 'ProjectViewController', 'updateSharing', array('project_id' => $project['id'], 'switch' => 'disable'), true, 'btn btn-red') ?>
<?php else: ?>
    <p class="alert alert-info"><?= t('This feature enables the iCal feed, RSS feed and the public board view.') ?></p>
    <?= $this->url->link(t('Enable public access'), 'ProjectViewController', 'updateSharing', array('project_id' => $project['id'], 'switch' => 'enable'), true, 'btn btn-blue') ?>
<?php endif ?>
