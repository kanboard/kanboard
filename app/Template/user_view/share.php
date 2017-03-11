<div class="page-header">
    <h2><?= t('Public access') ?></h2>
</div>

<?php if (! empty($user['token'])): ?>
    <div class="panel">
        <ul class="no-bullet">
            <li><strong><?= $this->url->icon('rss-square', t('RSS feed'), 'FeedController', 'user', array('token' => $user['token']), false, '', '', true) ?></strong></li>
            <li><strong><?= $this->url->icon('calendar', t('iCal feed'), 'ICalendarController', 'user', array('token' => $user['token']), false, '', '', true) ?></strong></li>
        </ul>
    </div>
    <?= $this->url->link(t('Disable public access'), 'UserViewController', 'share', array('user_id' => $user['id'], 'switch' => 'disable'), true, 'btn btn-red js-modal-replace') ?>
<?php else: ?>
    <?= $this->url->link(t('Enable public access'), 'UserViewController', 'share', array('user_id' => $user['id'], 'switch' => 'enable'), true, 'btn btn-blue js-modal-replace') ?>
<?php endif ?>
