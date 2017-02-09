<div class="page-header">
    <h2><?= t('Summary') ?></h2>
</div>
<ul class="panel">
    <li><strong><?= $project['is_active'] ? t('This project is open') : t('This project is closed') ?></strong></li>

    <?php if ($project['owner_id'] > 0): ?>
        <li><?= t('Project owner: ') ?><strong><?= $this->text->e($project['owner_name'] ?: $project['owner_username']) ?></strong></li>
    <?php endif ?>

    <?php if ($project['is_private']): ?>
        <li><i class="fa fa-lock"></i> <?= t('This project is private') ?></li>
    <?php endif ?>

    <?php if ($project['is_public']): ?>
        <li><?= $this->url->icon('share-alt', t('Public link'), 'BoardViewController', 'readonly', array('token' => $project['token']), false, '', '', true) ?></li>
        <li><?= $this->url->icon('rss-square', t('RSS feed'), 'FeedController', 'project', array('token' => $project['token']), false, '', '', true) ?></li>
        <li><?= $this->url->icon('calendar', t('iCal feed'), 'ICalendarController', 'project', array('token' => $project['token'])) ?></li>
    <?php else: ?>
        <li><?= t('Public access disabled') ?></li>
    <?php endif ?>

    <?php if ($project['last_modified']): ?>
        <li><?= t('Modified:').' '.$this->dt->datetime($project['last_modified']) ?></li>
    <?php endif ?>

    <?php if ($project['start_date']): ?>
        <li><?= t('Start date: ').$this->dt->date($project['start_date']) ?></li>
    <?php endif ?>

    <?php if ($project['end_date']): ?>
        <li><?= t('End date: ').$this->dt->date($project['end_date']) ?></li>
    <?php endif ?>
</ul>

<?php if (! empty($project['description'])): ?>
    <div class="page-header">
        <h2><?= t('Description') ?></h2>
    </div>

    <article class="markdown">
        <?= $this->text->markdown($project['description']) ?>
    </article>
<?php endif ?>

<div class="page-header">
    <h2><?= t('Columns') ?></h2>
</div>
<?php if (empty($columns)): ?>
    <p class="alert alert-error"><?= t('Your board doesn\'t have any columns!') ?></p>
<?php else: ?>
    <table class="table-striped table-scrolling"
        <thead>
        <tr>
            <th class="column-40"><?= t('Column') ?></th>
            <th class="column-10"><?= t('Task limit') ?></th>
            <th class="column-20"><?= t('Visible on dashboard') ?></th>
            <th class="column-15"><?= t('Open tasks') ?></th>
            <th class="column-15"><?= t('Closed tasks') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($columns as $column): ?>
            <tr data-column-id="<?= $column['id'] ?>">
                <td>
                    <?= $this->text->e($column['title']) ?>
                    <?php if (! empty($column['description'])): ?>
                        <span class="tooltip" title="<?= $this->text->markdownAttribute($column['description']) ?>">
                        <i class="fa fa-info-circle"></i>
                    </span>
                    <?php endif ?>
                </td>
                <td>
                    <?= $column['task_limit'] ?: '∞' ?>
                </td>
                <td>
                    <?= $column['hide_in_dashboard'] == 0 ? t('Yes') : t('No') ?>
                </td>
                <td>
                    <?= $column['nb_open_tasks'] ?>
                </td>
                <td>
                    <?= $column['nb_closed_tasks'] ?>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?>
