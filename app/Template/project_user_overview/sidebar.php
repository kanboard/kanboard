<div class="sidebar">
    <?= $this->form->select(
            'user_id',
            $users,
            $filter,
            array(),
            array('data-redirect-url="'.$this->url->href('ProjectUserOverviewController', $this->app->getRouterAction(), array('user_id' => 'USER_ID')).'"', 'data-redirect-regex="USER_ID"'),
            'chosen-select select-auto-redirect'
        ) ?>

    <br><br>
    <ul>
        <li <?= $this->app->checkMenuSelection('ProjectUserOverviewController', 'managers') ?>>
            <?= $this->url->link(t('Project managers'), 'ProjectUserOverviewController', 'managers', $filter) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ProjectUserOverviewController', 'members') ?>>
            <?= $this->url->link(t('Project members'), 'ProjectUserOverviewController', 'members', $filter) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ProjectUserOverviewController', 'opens') ?>>
            <?= $this->url->link(t('Open tasks'), 'ProjectUserOverviewController', 'opens', $filter) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ProjectUserOverviewController', 'closed') ?>>
            <?= $this->url->link(t('Closed tasks'), 'ProjectUserOverviewController', 'closed', $filter) ?>
        </li>

        <?= $this->hook->render('template:project-user:sidebar') ?>
    </ul>
</div>
