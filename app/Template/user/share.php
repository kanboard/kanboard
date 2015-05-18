<div class="page-header">
    <h2><?= t('Public access') ?></h2>
</div>

<?php if (! empty($user['token'])): ?>

    <div class="listing">
        <ul class="no-bullet">
            <li><strong><i class="fa fa-calendar"></i> <?= $this->a(t('iCalendar (iCal format, *.ics)'), 'ical', 'user', array('token' => $user['token']), false, '', '', true) ?></strong></li>
        </ul>
    </div>

    <?= $this->a(t('Disable public access'), 'user', 'share', array('user_id' => $user['id'], 'switch' => 'disable'), true, 'btn btn-red') ?>

<?php else: ?>
    <?= $this->a(t('Enable public access'), 'user', 'share', array('user_id' => $user['id'], 'switch' => 'enable'), true, 'btn btn-blue') ?>
<?php endif ?>
