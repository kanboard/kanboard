<li>
    <i class="fa fa-search fa-fw"></i>
    <?= $this->url->link(t('Search'), 'projectinfo', 'search', array('project_id' => $project['id'])) ?>
</li>
<li>
    <i class="fa fa-check-square-o fa-fw"></i>
    <?= $this->url->link(t('Completed tasks'), 'projectinfo', 'tasks', array('project_id' => $project['id'])) ?>
</li>
<li>
    <i class="fa fa-check-square-o fa-fw"></i>
    <?= $this->url->link(t('List view'), 'projectinfo', 'listView', array('project_id' => $project['id'])) ?>
</li>
<li>
    <i class="fa fa-dashboard fa-fw"></i>
    <?= $this->url->link(t('Activity'), 'projectinfo', 'activity', array('project_id' => $project['id'])) ?>
</li>
<li>
    <i class="fa fa-calendar fa-fw"></i>
    <?= $this->url->link(t('Calendar'), 'calendar', 'show', array('project_id' => $project['id'])) ?>
</li>

<?php if ($project['is_public']): ?>
<li>
    <i class="fa fa-share-alt fa-fw"></i> <?= $this->url->link(t('Public link'), 'board', 'readonly', array('token' => $project['token']), false, '', '', true) ?>
</li>
<?php endif ?>

<?php if ($this->user->isManager($project['id'])): ?>
<li>
    <i class="fa fa-line-chart fa-fw"></i>
    <?= $this->url->link(t('Analytics'), 'analytic', 'tasks', array('project_id' => $project['id'])) ?>
</li>
<li>
    <i class="fa fa-pie-chart fa-fw"></i>
    <?= $this->url->link(t('Budget'), 'budget', 'index', array('project_id' => $project['id'])) ?>
</li>
<li>
    <i class="fa fa-download fa-fw"></i>
    <?= $this->url->link(t('Exports'), 'export', 'tasks', array('project_id' => $project['id'])) ?>
</li>
<li>
    <i class="fa fa-cog fa-fw"></i>
    <?= $this->url->link(t('Settings'), 'project', 'show', array('project_id' => $project['id'])) ?>
</li>
<?php endif ?>
