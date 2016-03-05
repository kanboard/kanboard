<div class="sidebar">
    <h2><?= $this->text->e($user['name'] ?: $user['username']) ?></h2>
    <ul>
        <li <?= $this->app->checkMenuSelection('app', 'index') ?>>
            <?= $this->url->link(t('Overview'), 'app', 'index', array('user_id' => $user['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('app', 'projects') ?>>
            <?= $this->url->link(t('My projects'), 'app', 'projects', array('user_id' => $user['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('app', 'tasks') ?>>
            <?= $this->url->link(t('My tasks'), 'app', 'tasks', array('user_id' => $user['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('app', 'subtasks') ?>>
            <?= $this->url->link(t('My subtasks'), 'app', 'subtasks', array('user_id' => $user['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('app', 'calendar') ?>>
            <?= $this->url->link(t('My calendar'), 'app', 'calendar', array('user_id' => $user['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('app', 'activity') ?>>
            <?= $this->url->link(t('My activity stream'), 'app', 'activity', array('user_id' => $user['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('app', 'notifications') ?>>
            <?= $this->url->link(t('My notifications'), 'app', 'notifications', array('user_id' => $user['id'])) ?>
        </li>
        <?= $this->hook->render('template:dashboard:sidebar') ?>
    </ul>
</div>