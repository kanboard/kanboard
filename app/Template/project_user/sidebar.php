<div class="sidebar">
    <h2><?= t('Actions') ?></h2>

    <?= $this->form->select(
            'user_id',
            $users,
            $filter,
            array(),
            array('data-redirect-url="'.$this->url->href('projectuser', $this->app->getRouterAction(), array('user_id' => 'USER_ID')).'"', 'data-redirect-regex="USER_ID"'),
            'chosen-select select-auto-redirect'
        ) ?>

    <br/><br/>
    <ul>
        <li <?= $this->app->getRouterAction() === 'managers' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Project managers'), 'projectuser', 'managers', $filter) ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'members' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Project members'), 'projectuser', 'members', $filter) ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'opens' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Open tasks'), 'projectuser', 'opens', $filter) ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'closed' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Closed tasks'), 'projectuser', 'closed', $filter) ?>
        </li>

        <?= $this->hook->render('template:project-user:sidebar') ?>
    </ul>
</div>