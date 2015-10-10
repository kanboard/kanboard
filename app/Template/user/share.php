<div class="page-header">
    <h2><?= t('Public access') ?></h2>
</div>

<?php if (! empty($user['token'])): ?>

    <div class="listing">
        <ul class="no-bullet">
            <li><strong><i class="fa fa-rss-square"></i> <?= $this->url->link(t('RSS feed'), 'feed', 'user', array('token' => $user['token']), false, '', '', true) ?></strong></li>
            <li><strong><i class="fa fa-calendar"></i> <?= $this->url->link(t('iCal feed'), 'ical', 'user', array('token' => $user['token']), false, '', '', true) ?></strong></li>
        </ul>
    </div>

    <?= $this->url->link(t('Disable public access'), 'user', 'share', array('user_id' => $user['id'], 'switch' => 'disable'), true, 'btn btn-red') ?>

<?php else: ?>
    <?= $this->url->link(t('Enable public access'), 'user', 'share', array('user_id' => $user['id'], 'switch' => 'enable'), true, 'btn btn-blue') ?>
<?php endif ?>
