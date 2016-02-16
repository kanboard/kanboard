<section id="main">
    <?= $this->render('project_header/header', array(
        'project' => $project,
        'filters' => $filters,
    )) ?>

    <div class="project-overview-columns">
        <?php foreach ($project['columns'] as $column): ?>
            <div class="project-overview-column">
                <strong title="<?= t('Task count') ?>"><?= $column['nb_tasks'] ?></strong><br>
                <span><?= $this->e($column['title']) ?></span>
            </div>
        <?php endforeach ?>
    </div>

    <?php if (! empty($project['description'])): ?>
        <div class="page-header">
            <h2><?= $this->e($project['name']) ?></h2>
        </div>
        <article class="markdown">
            <?= $this->text->markdown($project['description']) ?>
        </article>
    <?php endif ?>

    <div class="page-header">
        <h2><?= t('Information') ?></h2>
    </div>
    <div class="listing">
    <ul>

        <?php if ($project['owner_id'] > 0): ?>
            <li><?= t('Project owner: ') ?><strong><?= $this->e($project['owner_name'] ?: $project['owner_username']) ?></strong></li>
        <?php endif ?>

        <?php if (! empty($users)): ?>
            <?php foreach ($roles as $role => $role_name): ?>
                <?php if (isset($users[$role])): ?>
                <li>
                    <?= $role_name ?>:
                    <strong><?= implode(', ', $users[$role]) ?></strong>
                </li>
                <?php endif ?>
            <?php endforeach ?>
        <?php endif ?>

        <?php if ($project['start_date']): ?>
            <li><?= t('Start date: ').$this->dt->date($project['start_date']) ?></li>
        <?php endif ?>

        <?php if ($project['end_date']): ?>
            <li><?= t('End date: ').$this->dt->date($project['end_date']) ?></li>
        <?php endif ?>

        <?php if ($project['is_public']): ?>
            <li><i class="fa fa-share-alt"></i> <?= $this->url->link(t('Public link'), 'board', 'readonly', array('token' => $project['token']), false, '', '', true) ?></li>
            <li><i class="fa fa-rss-square"></i> <?= $this->url->link(t('RSS feed'), 'feed', 'project', array('token' => $project['token']), false, '', '', true) ?></li>
            <li><i class="fa fa-calendar"></i> <?= $this->url->link(t('iCal feed'), 'ical', 'project', array('token' => $project['token'])) ?></li>
        <?php endif ?>
    </ul>
    </div>

    <div class="page-header">
        <h2><?= t('Last activity') ?></h2>
    </div>
    <?= $this->render('event/events', array('events' => $events)) ?>
</section>
