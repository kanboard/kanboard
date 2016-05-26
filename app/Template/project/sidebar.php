<div class="sidebar">
    <h2><?= t('Actions') ?></h2>
    <ul>
        <li <?= $this->app->checkMenuSelection('ProjectViewController', 'show') ?>>
            <?= $this->url->link(t('Summary'), 'ProjectViewController', 'show', array('project_id' => $project['id'])) ?>
        </li>
        <?php if ($this->user->hasProjectAccess('customfilter', 'index', $project['id'])): ?>
        <li <?= $this->app->checkMenuSelection('customfilter') ?>>
            <?= $this->url->link(t('Custom filters'), 'customfilter', 'index', array('project_id' => $project['id'])) ?>
        </li>
        <?php endif ?>

        <?php if ($this->user->hasProjectAccess('ProjectEdit', 'edit', $project['id'])): ?>
            <li <?= $this->app->checkMenuSelection('ProjectEdit') ?>>
                <?= $this->url->link(t('Edit project'), 'ProjectEdit', 'edit', array('project_id' => $project['id'])) ?>
            </li>
            <li <?= $this->app->checkMenuSelection('ProjectViewController', 'share') ?>>
                <?= $this->url->link(t('Public access'), 'ProjectViewController', 'share', array('project_id' => $project['id'])) ?>
            </li>
            <li <?= $this->app->checkMenuSelection('ProjectViewController', 'notifications') ?>>
                <?= $this->url->link(t('Notifications'), 'ProjectViewController', 'notifications', array('project_id' => $project['id'])) ?>
            </li>
            <li <?= $this->app->checkMenuSelection('ProjectViewController', 'integrations') ?>>
                <?= $this->url->link(t('Integrations'), 'ProjectViewController', 'integrations', array('project_id' => $project['id'])) ?>
            </li>
            <li <?= $this->app->checkMenuSelection('column') ?>>
                <?= $this->url->link(t('Columns'), 'column', 'index', array('project_id' => $project['id'])) ?>
            </li>
            <li <?= $this->app->checkMenuSelection('swimlane') ?>>
                <?= $this->url->link(t('Swimlanes'), 'swimlane', 'index', array('project_id' => $project['id'])) ?>
            </li>
            <li <?= $this->app->checkMenuSelection('category') ?>>
                <?= $this->url->link(t('Categories'), 'category', 'index', array('project_id' => $project['id'])) ?>
            </li>
            <?php if ($project['is_private'] == 0): ?>
            <li <?= $this->app->checkMenuSelection('ProjectPermission') ?>>
                <?= $this->url->link(t('Permissions'), 'ProjectPermissionController', 'index', array('project_id' => $project['id'])) ?>
            </li>
            <?php endif ?>
            <li <?= $this->app->checkMenuSelection('action') ?>>
                <?= $this->url->link(t('Automatic actions'), 'action', 'index', array('project_id' => $project['id'])) ?>
            </li>
            <li <?= $this->app->checkMenuSelection('ProjectViewController', 'duplicate') ?>>
                <?= $this->url->link(t('Duplicate'), 'ProjectViewController', 'duplicate', array('project_id' => $project['id'])) ?>
            </li>
                <?php if ($project['is_active']): ?>
                    <li>
                    <?= $this->url->link(t('Disable'), 'ProjectStatusController', 'confirmDisable', array('project_id' => $project['id']), false, 'popover') ?>
                <?php else: ?>
                    <li>
                    <?= $this->url->link(t('Enable'), 'ProjectStatusController', 'confirmEnable', array('project_id' => $project['id']), false, 'popover') ?>
                <?php endif ?>
            </li>
            <li <?= $this->app->checkMenuSelection('taskImport') ?>>
                <?= $this->url->link(t('Import'), 'taskImport', 'step1', array('project_id' => $project['id'])) ?>
            </li>
            <?php if ($this->user->hasProjectAccess('ProjectStatusController', 'remove', $project['id'])): ?>
                <li>
                    <?= $this->url->link(t('Remove'), 'ProjectStatusController', 'confirmRemove', array('project_id' => $project['id']), false, 'popover') ?>
                </li>
            <?php endif ?>
        <?php endif ?>

        <?= $this->hook->render('template:project:sidebar', array('project' => $project)) ?>
    </ul>
</div>
