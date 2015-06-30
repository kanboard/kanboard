<div class="sidebar">
    <h2><?= t('Exports') ?></h2>
    <ul>
        <li>
            <?= $this->url->link(t('Tasks'), 'export', 'tasks', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= $this->url->link(t('Subtasks'), 'export', 'subtasks', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= $this->url->link(t('Task transitions'), 'export', 'transitions', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= $this->url->link(t('Daily project summary'), 'export', 'summary', array('project_id' => $project['id'])) ?>
        </li>
    </ul>
</div>