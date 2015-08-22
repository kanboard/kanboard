<div class="page-header">
    <h2><?= t('Summary') ?></h2>
</div>
<ul class="listing">
    <li><strong><?= $project['is_active'] ? t('Active') : t('Inactive') ?></strong></li>

    <?php if ($project['is_private']): ?>
        <li><i class="fa fa-lock"></i> <?= t('This project is private') ?></li>
    <?php endif ?>

    <?php if ($project['is_public']): ?>
        <li><i class="fa fa-share-alt"></i> <?= $this->url->link(t('Public link'), 'board', 'readonly', array('token' => $project['token']), false, '', '', true) ?></li>
        <li><i class="fa fa-rss-square"></i> <?= $this->url->link(t('RSS feed'), 'feed', 'project', array('token' => $project['token']), false, '', '', true) ?></li>
        <li><i class="fa fa-calendar"></i> <?= $this->url->link(t('iCal feed'), 'ical', 'project', array('token' => $project['token'])) ?></li>
    <?php else: ?>
        <li><?= t('Public access disabled') ?></li>
    <?php endif ?>

    <?php if ($project['last_modified']): ?>
        <li><?= dt('Last modified on %B %e, %Y at %k:%M %p', $project['last_modified']) ?></li>
    <?php endif ?>

    <?php if ($project['start_date']): ?>
        <li><?= t('Start date: %s', $project['start_date']) ?></li>
    <?php endif ?>

    <?php if ($project['end_date']): ?>
        <li><?= t('End date: %s', $project['end_date']) ?></li>
    <?php endif ?>

    <?php if ($stats['nb_tasks'] > 0): ?>

        <?php if ($stats['nb_active_tasks'] > 0): ?>
            <li><?= $this->url->link(t('%d tasks on the board', $stats['nb_active_tasks']), 'board', 'show', array('project_id' => $project['id'], 'search' => 'status:open')) ?></li>
        <?php endif ?>

        <?php if ($stats['nb_inactive_tasks'] > 0): ?>
            <li><?= $this->url->link(t('%d closed tasks', $stats['nb_inactive_tasks']), 'listing', 'show', array('project_id' => $project['id'], 'search' => 'status:closed')) ?></li>
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
        <th class="column-60"><?= t('Column') ?></th>
        <th class="column-20"><?= t('Task limit') ?></th>
        <th class="column-20"><?= t('Active tasks') ?></th>
    </tr>
    <?php foreach ($stats['columns'] as $column): ?>
    <tr>
        <td>
            <?= $this->e($column['title']) ?>
            <?php if (! empty($column['description'])): ?>
                <span class="tooltip" title='<?= $this->e($this->text->markdown($column['description'])) ?>'>
                    <i class="fa fa-info-circle"></i>
                </span>
            <?php endif ?>
        </td>
        <td><?= $column['task_limit'] ?: '∞' ?></td>
        <td><?= $column['nb_active_tasks'] ?></td>
    </tr>
    <?php endforeach ?>
</table>

<?php if (! empty($project['description'])): ?>
    <div class="page-header">
        <h2><?= t('Description') ?></h2>
    </div>

    <article class="markdown">
        <?= $this->text->markdown($project['description']) ?>
    </article>
<?php endif ?>
