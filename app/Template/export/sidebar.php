<div class="sidebar">
    <ul>
        <li <?= $this->app->checkMenuSelection('ExportController', 'tasks') ?>>
            <?= $this->url->link(t('Tasks'), 'ExportController', 'tasks', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ExportController', 'subtasks') ?>>
            <?= $this->url->link(t('Subtasks'), 'ExportController', 'subtasks', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ExportController', 'transitions') ?>>
            <?= $this->url->link(t('Task transitions'), 'ExportController', 'transitions', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ExportController', 'summary') ?>>
            <?= $this->url->link(t('Daily project summary'), 'ExportController', 'summary', array('project_id' => $project['id'])) ?>
        </li>
        <?= $this->hook->render('template:export:sidebar') ?>
    </ul>
</div>
