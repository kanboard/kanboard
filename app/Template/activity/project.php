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
                <i class="fa fa-th fa-fw"></i>
                <?= $this->url->link(t('Back to the board'), 'board', 'show', array('project_id' => $project['id'])) ?>
            </li>
            <li>
                <i class="fa fa-calendar fa-fw"></i>
                <?= $this->url->link(t('Back to the calendar'), 'calendar', 'show', array('project_id' => $project['id'])) ?>
            </li>
            <?php if ($this->user->hasProjectAccess('ProjectEdit', 'edit', $project['id'])): ?>
            <li>
                <i class="fa fa-cog fa-fw"></i>
                <?= $this->url->link(t('Project settings'), 'project', 'show', array('project_id' => $project['id'])) ?>
            </li>
            <?php endif ?>
            <li>
                <i class="fa fa-folder fa-fw"></i>
                <?= $this->url->link(t('All projects'), 'project', 'index') ?>
            </li>
            <?php if ($project['is_public']): ?>
                <li><i class="fa fa-rss-square fa-fw"></i><?= $this->url->link(t('RSS feed'), 'feed', 'project', array('token' => $project['token']), false, '', '', true) ?></li>
                <li><i class="fa fa-calendar fa-fw"></i><?= $this->url->link(t('iCal feed'), 'ical', 'project', array('token' => $project['token'])) ?></li>
            <?php endif ?>
        </ul>
    </div>

    <div class="search">
        <form method="get" action="<?= $this->url->dir() ?>" class="search">
            <?= $this->form->hidden('controller', array('controller' => 'search')) ?>
            <?= $this->form->hidden('action', array('action' => 'activity')) ?>
            <?= $this->form->text('search', array(), array(), array('placeholder="'.t('Search').'"'), 'form-input-large') ?>
            <?= $this->render('app/activity_filters_helper') ?>
        </form>
    </div>

    <?php if (!$events->isEmpty()): ?>
    <div class="page-header">
        <ul>
            <li>
                <?= $events->order(t('Order by Date'), 'id') ?>
            </li>
            <li>
                <?= $events->order(t('Order by Task'), 'task_id') ?>
            </li>
        </ul>
    </div>
    <?php endif ?>
    <?= $this->render('event/events', array('events' => $events)) ?>
</section>
