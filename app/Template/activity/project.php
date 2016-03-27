<section id="main">
    <?= $this->projectHeader->render($project, 'Analytic', $this->app->getRouterAction()) ?>

    <?php if ($project['is_public']): ?>
    <div class="menu-inline pull-right">
        <ul>
            <li><i class="fa fa-rss-square fa-fw"></i><?= $this->url->link(t('RSS feed'), 'feed', 'project', array('token' => $project['token']), false, '', '', true) ?></li>
            <li><i class="fa fa-calendar fa-fw"></i><?= $this->url->link(t('iCal feed'), 'ical', 'project', array('token' => $project['token'])) ?></li>
        </ul>
    </div>
    <?php endif ?>

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
