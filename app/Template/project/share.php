<div class="page-header">
    <h2><?= t('Public access') ?></h2>
</div>

<?php if ($project['is_public']): ?>

    <div class="listing">
        <ul class="no-bullet">
            <li><strong><i class="fa fa-share-alt"></i> <?= Helper\a(t('Public link'), 'board', 'readonly', array('token' => $project['token']), false, '', '', true) ?></strong></li>
            <li><strong><i class="fa fa-rss-square"></i> <?= Helper\a(t('RSS feed'), 'project', 'feed', array('token' => $project['token']), false, '', '', true) ?></strong></li>
        </ul>
        <input type="text" class="auto-select" readonly="readonly" value="<?= Helper\get_current_base_url().Helper\u('board', 'readonly', array('token' => $project['token'])) ?>"/>
    </div>

    <?= Helper\a(t('Disable public access'), 'project', 'share', array('project_id' => $project['id'], 'switch' => 'disable'), true, 'btn btn-red') ?>

<?php else: ?>
    <?= Helper\a(t('Enable public access'), 'project', 'share', array('project_id' => $project['id'], 'switch' => 'enable'), true, 'btn btn-blue') ?>
<?php endif ?>
