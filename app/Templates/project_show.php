<div class="page-header">
    <h2><?= t('Summary') ?></h2>
</div>
<ul class="settings">
    <li><strong><?= $project['is_active'] ? t('Active') : t('Inactive') ?></strong></li>

    <?php if ($project['is_public']): ?>
        <li><i class="fa fa-share-alt"></i>  <a href="?controller=board&amp;action=readonly&amp;token=<?= $project['token'] ?>" target="_blank"><?= t('Public link') ?></a></li>
        <li><i class="fa fa-rss-square"></i> <a href="?controller=project&amp;action=feed&amp;token=<?= $project['token'] ?>" target="_blank"><?= t('RSS feed') ?></a></li>
    <?php else: ?>
        <li><?= t('Public access disabled') ?></li>
    <?php endif ?>

    <?php if ($project['last_modified']): ?>
        <li><?= dt('Last modified on %B %e, %Y at %k:%M %p', $project['last_modified']) ?></li>
    <?php endif ?>

    <?php if ($stats['nb_tasks'] > 0): ?>

        <?php if ($stats['nb_active_tasks'] > 0): ?>
            <li><a href="?controller=board&amp;action=show&amp;project_id=<?= $project['id'] ?>"><?= t('%d tasks on the board', $stats['nb_active_tasks']) ?></a></li>
        <?php endif ?>

        <?php if ($stats['nb_inactive_tasks'] > 0): ?>
            <li><a href="?controller=project&amp;action=tasks&amp;project_id=<?= $project['id'] ?>"><?= t('%d closed tasks', $stats['nb_inactive_tasks']) ?></a></li>
        <?php endif ?>

        <li><?= t('%d tasks in total', $stats['nb_tasks']) ?></li>

    <?php else: ?>
        <li><?= t('No task for this project') ?></li>
    <?php endif ?>
</ul>

<div class="page-header">
    <h2><?= t('Board') ?></h2>
</div>
<table class="table-stripped">
    <tr>
        <th width="50%"><?= t('Column') ?></th>
        <th><?= t('Task limit') ?></th>
        <th><?= t('Active tasks') ?></th>
    </tr>
    <?php foreach ($stats['columns'] as $column): ?>
    <tr>
        <td><?= Helper\escape($column['title']) ?></td>
        <td><?= $column['task_limit'] ?: '∞' ?></td>
        <td><?= $column['nb_active_tasks'] ?></td>
    </tr>
    <?php endforeach ?>
</table>
