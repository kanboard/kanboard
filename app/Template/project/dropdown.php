<li>
    <i class="fa fa-dashboard fa-fw"></i>&nbsp;
    <?= $this->url->link(t('Activity'), 'activity', 'project', array('project_id' => $project['id'])) ?>
</li>
<li>
    <i class="fa fa-filter fa-fw"></i>&nbsp;
    <?= $this->url->link(t('Custom filters'), 'customfilter', 'index', array('project_id' => $project['id'])) ?>
</li>

<?php if ($project['is_public']): ?>
<li>
    <i class="fa fa-share-alt fa-fw"></i>&nbsp;<?= $this->url->link(t('Public link'), 'board', 'readonly', array('token' => $project['token']), false, '', '', true) ?>
</li>
<?php endif ?>

<?= $this->hook->render('template:project:dropdown', array('project' => $project)) ?>

<?php if ($this->user->isProjectManagementAllowed($project['id'])): ?>
    <li>
        <i class="fa fa-line-chart fa-fw"></i>&nbsp;
        <?= $this->url->link(t('Analytics'), 'analytic', 'tasks', array('project_id' => $project['id'])) ?>
    </li>
    <li>
        <i class="fa fa-download fa-fw"></i>&nbsp;
        <?= $this->url->link(t('Exports'), 'export', 'tasks', array('project_id' => $project['id'])) ?>
    </li>
    <li>
        <i class="fa fa-cog fa-fw"></i>&nbsp;
        <?= $this->url->link(t('Settings'), 'project', 'show', array('project_id' => $project['id'])) ?>
    </li>
<?php endif ?>
