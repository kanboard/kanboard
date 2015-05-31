<section id="main">
    <div class="page-header">
        <ul>
            <li>
            <span class="dropdown">
                <span>
                    <i class="fa fa-caret-down"></i> <a href="#" class="dropdown-menu"><?= t('Actions') ?></a>
                    <ul>
                        <?= $this->render('project/dropdown', array('project' => $project)) ?>
                    </ul>
                </span>
            </span>
            </li>
            <li>
                <i class="fa fa-table fa-fw"></i>
                <?= $this->url->link(t('Back to the board'), 'board', 'show', array('project_id' => $project['id'])) ?>
            </li>
            <li>
                <i class="fa fa-folder fa-fw"></i>
                <?= $this->url->link(t('All projects'), 'project', 'index') ?>
            </li>
            <?php if ($project['is_public']): ?>
                <li><i class="fa fa-rss-square fa-fw"></i><?= $this->url->link(t('RSS feed'), 'project', 'feed', array('token' => $project['token']), false, '', '', true) ?></li>
                <li><i class="fa fa-calendar fa-fw"></i><?= $this->url->link(t('iCal feed'), 'ical', 'project', array('token' => $project['token'])) ?></li>
            <?php endif ?>
        </ul>
    </div>

    <?= $this->render('event/events', array('events' => $events)) ?>
</section>