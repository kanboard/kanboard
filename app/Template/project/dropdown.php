<li>
    <?= $this->url->button('fa-dashboard', t('Activity'), 'activity', 'project', array('project_id' => $project['id'])) ?>
</li>

<?php if ($this->user->hasProjectAccess('customfilter', 'index', $project['id'])): ?>
<li>
    <?= $this->url->button('fa-filter', t('Custom filters'), 'customfilter', 'index', array('project_id' => $project['id'])) ?>
</li>
<?php endif ?>

<?php if ($project['is_public']): ?>
<li>
    <?= $this->url->button('fa-share-alt', t('Public link'), 'board', 'readonly', array('token' => $project['token']), false, '', '', true) ?>
</li>
<?php endif ?>

<?= $this->hook->render('template:project:dropdown', array('project' => $project)) ?>

<?php if ($this->user->hasProjectAccess('analytic', 'tasks', $project['id'])): ?>
    <li>
        <?= $this->url->button('fa-line-chart', t('Analytics'), 'analytic', 'tasks', array('project_id' => $project['id'])) ?>
    </li>
<?php endif ?>

<?php if ($this->user->hasProjectAccess('export', 'tasks', $project['id'])): ?>
    <li>
        <?= $this->url->button('fa-download', t('Exports'), 'export', 'tasks', array('project_id' => $project['id'])) ?>
    </li>
<?php endif ?>

<?php if ($this->user->hasProjectAccess('ProjectEdit', 'edit', $project['id'])): ?>
    <li>
        <?= $this->url->button('fa-cog', t('Settings'), 'project', 'show', array('project_id' => $project['id'])) ?>
    </li>
<?php endif ?>
