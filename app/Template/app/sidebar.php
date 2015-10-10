<div class="sidebar">
    <h2><?= $this->e($user['name'] ?: $user['username']) ?></h2>
    <ul>
        <li <?= $this->app->getRouterAction() === 'index' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Overview'), 'app', 'index', array('user_id' => $user['id'])) ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'projects' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('My projects'), 'app', 'projects', array('user_id' => $user['id'])) ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'tasks' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('My tasks'), 'app', 'tasks', array('user_id' => $user['id'])) ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'subtasks' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('My subtasks'), 'app', 'subtasks', array('user_id' => $user['id'])) ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'calendar' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('My calendar'), 'app', 'calendar', array('user_id' => $user['id'])) ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'activity' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('My activity stream'), 'app', 'activity', array('user_id' => $user['id'])) ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'notifications' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('My notifications'), 'app', 'notifications', array('user_id' => $user['id'])) ?>
        </li>
        <?= $this->hook->render('template:dashboard:sidebar') ?>
    </ul>
    <div class="sidebar-collapse"><a href="#" title="<?= t('Hide sidebar') ?>"><i class="fa fa-chevron-left"></i></a></div>
    <div class="sidebar-expand" style="display: none"><a href="#" title="<?= t('Expand sidebar') ?>"><i class="fa fa-chevron-right"></i></a></div>
</div>