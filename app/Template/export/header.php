<div class="page-header">
    <h2><?= $this->text->e($project['name']) ?> &gt; <?= $title ?></h2>
    <ul>
        <li <?= $this->app->checkMenuSelection('ExportController', 'tasks') ?>>
            <?= $this->modal->replaceLink(t('Tasks'), 'ExportController', 'tasks', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ExportController', 'subtasks') ?>>
            <?= $this->modal->replaceLink(t('Subtasks'), 'ExportController', 'subtasks', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ExportController', 'transitions') ?>>
            <?= $this->modal->replaceLink(t('Task transitions'), 'ExportController', 'transitions', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ExportController', 'summary') ?>>
            <?= $this->modal->replaceLink(t('Daily project summary'), 'ExportController', 'summary', array('project_id' => $project['id'])) ?>
        </li>
        <?= $this->hook->render('template:export:header', array('project_id' => $project['id'])) ?>
    </ul>
</div>
