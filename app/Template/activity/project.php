<section id="main">
    <div class="page-header">
        <ul>
            <li>
            <span class="dropdown">
                <span>
                    <a href="#" class="dropdown-menu btn"><?= t('Actions') ?> <i class="fa fa-caret-down"></i></a>
                    <ul>
                        <?= $this->render('project/dropdown', array('project' => $project)) ?>
                    </ul>
                </span>
            </span>
            </li>
        </ul>
        <ul class="btn-group">
            <li>
                <?= $this->url->buttonLink('<fa-th>' . t('Back to the board'), 'board', 'show', array('project_id' => $project['id'])) ?>
            </li>
            <li>
                <?= $this->url->buttonLink('<fa-calendar>' . t('Back to the calendar'), 'calendar', 'show', array('project_id' => $project['id'])) ?>
            </li>
            <?php if ($this->user->hasProjectAccess('ProjectEdit', 'edit', $project['id'])): ?>
            <li>
                <?= $this->url->buttonLink('<fa-cog>' . t('Project settings'), 'project', 'show', array('project_id' => $project['id'])) ?>
            </li>
            <?php endif ?>
            <li>
                <?= $this->url->buttonLink('<fa-folder>' . t('All projects'), 'project', 'index') ?>
            </li>
            <?php if ($project['is_public']): ?>
                <li><?= $this->url->buttonLink('<fa-rss-square>' . t('RSS feed'), 'feed', 'project', array('token' => $project['token']), false, '', '', true) ?></li>
                <li><?= $this->url->buttonLink('<fa-calendar>' . t('iCal feed'), 'ical', 'project', array('token' => $project['token'])) ?></li>
            <?php endif ?>
        </ul>
    </div>

    <?= $this->render('event/events', array('events' => $events)) ?>
</section>
