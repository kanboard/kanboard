<div class="page-header">
    <h2><?= t('Public access') ?></h2>
</div>

<?php if ($project['is_public']): ?>

    <div class="settings">
        <strong><a href="?controller=board&amp;action=readonly&amp;token=<?= $project['token'] ?>" target="_blank"><?= t('Public link') ?></a></strong><br/>
        <input type="text" readonly="readonly" value="<?= Helper\get_current_base_url() ?>?controller=board&amp;action=readonly&amp;token=<?= $project['token'] ?>"/>
    </div>

    <a href="?controller=project&amp;action=disablePublic&amp;project_id=<?= $project['id'].Helper\param_csrf() ?>" class="btn btn-red"><?= t('Disable public access') ?></a>

<?php else: ?>

    <a href="?controller=project&amp;action=enablePublic&amp;project_id=<?= $project['id'].Helper\param_csrf() ?>" class="btn btn-blue"><?= t('Enable public access') ?></a>

<?php endif ?>
