<div class="page-header">
    <h2><?= t('Public access') ?></h2>
</div>

<?php if ($project['is_public']): ?>

    <div class="listing">
        <ul class="no-bullet">
            <li><strong><i class="fa fa-share-alt"></i> <?= $this->a(t('Public link'), 'board', 'readonly', array('token' => $project['token']), false, '', '', true) ?></strong></li>
            <li><strong><i class="fa fa-rss-square"></i> <?= $this->a(t('RSS feed'), 'project', 'feed', array('token' => $project['token']), false, '', '', true) ?></strong></li>
            <li><strong><i class="fa fa-calendar"></i> <?= $this->a(t('iCalendar (iCal format, *.ics)'), 'ical', 'project', array('token' => $project['token']), false, '', '', true) ?></strong></li>
        </ul>
    </div>

    <?= $this->a(t('Disable public access'), 'project', 'share', array('project_id' => $project['id'], 'switch' => 'disable'), true, 'btn btn-red') ?>

<?php else: ?>
    <?= $this->a(t('Enable public access'), 'project', 'share', array('project_id' => $project['id'], 'switch' => 'enable'), true, 'btn btn-blue') ?>
<?php endif ?>
