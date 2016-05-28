<div class="sidebar">
    <h2><?= t('Exports') ?></h2>
    <ul>
        <li <?= $this->app->getRouterAction() === 'tasks' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Tasks'), 'ExportController', 'tasks', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'subtasks' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Subtasks'), 'ExportController', 'subtasks', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'transitions' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Task transitions'), 'ExportController', 'transitions', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->getRouterAction() === 'summary' ? 'class="active"' : '' ?>>
            <?= $this->url->link(t('Daily project summary'), 'ExportController', 'summary', array('project_id' => $project['id'])) ?>
        </li>
        <?= $this->hook->render('template:export:sidebar') ?>
    </ul>
</div>
