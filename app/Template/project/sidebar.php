<div class="sidebar">
    <h2><?= t('Actions') ?></h2>
    <ul>
        <li <?= $this->app->checkMenuSelection('project', 'show') ?>>
            <?= $this->url->link(t('Summary'), 'project', 'show', array('project_id' => $project['id'])) ?>
        </li>
        <?php if ($this->user->hasProjectAccess('customfilter', 'index', $project['id'])): ?>
        <li <?= $this->app->checkMenuSelection('customfilter') ?>>
            <?= $this->url->link(t('Custom filters'), 'customfilter', 'index', array('project_id' => $project['id'])) ?>
        </li>
        <?php endif ?>

        <?php if ($this->user->hasProjectAccess('project', 'edit', $project['id'])): ?>
            <li <?= $this->app->checkMenuSelection('project', 'share') ?>>
                <?= $this->url->link(t('Public access'), 'project', 'share', array('project_id' => $project['id'])) ?>
            </li>
            <li <?= $this->app->checkMenuSelection('project', 'notifications') ?>>
                <?= $this->url->link(t('Notifications'), 'project', 'notifications', array('project_id' => $project['id'])) ?>
            </li>
            <li <?= $this->app->checkMenuSelection('project', 'integrations') ?>>
                <?= $this->url->link(t('Integrations'), 'project', 'integrations', array('project_id' => $project['id'])) ?>
            </li>
            <li <?= $this->app->checkMenuSelection('project', 'edit') ?>>
                <?= $this->url->link(t('Edit project'), 'project', 'edit', array('project_id' => $project['id'])) ?>
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
                <?= $this->url->link(t('Permissions'), 'ProjectPermission', 'index', array('project_id' => $project['id'])) ?>
            </li>
            <?php endif ?>
            <li <?= $this->app->checkMenuSelection('action') ?>>
                <?= $this->url->link(t('Automatic actions'), 'action', 'index', array('project_id' => $project['id'])) ?>
            </li>
            <li <?= $this->app->checkMenuSelection('project', 'duplicate') ?>>
                <?= $this->url->link(t('Duplicate'), 'project', 'duplicate', array('project_id' => $project['id'])) ?>
            </li>
                <?php if ($project['is_active']): ?>
                    <li <?= $this->app->checkMenuSelection('project', 'disable') ?>>
                    <?= $this->url->link(t('Disable'), 'project', 'disable', array('project_id' => $project['id']), true) ?>
                <?php else: ?>
                    <li <?= $this->app->checkMenuSelection('project', 'enable') ?>>
                    <?= $this->url->link(t('Enable'), 'project', 'enable', array('project_id' => $project['id']), true) ?>
                <?php endif ?>
            </li>
            <li <?= $this->app->checkMenuSelection('taskImport') ?>>
                <?= $this->url->link(t('Import'), 'taskImport', 'step1', array('project_id' => $project['id'])) ?>
            </li>
            <?php if ($this->user->hasProjectAccess('project', 'remove', $project['id'])): ?>
                <li <?= $this->app->checkMenuSelection('project', 'remove') ?>>
                    <?= $this->url->link(t('Remove'), 'project', 'remove', array('project_id' => $project['id'])) ?>
                </li>
            <?php endif ?>
        <?php endif ?>

        <?= $this->hook->render('template:project:sidebar', array('project' => $project)) ?>
    </ul>
    <div class="sidebar-collapse"><a href="#" title="<?= t('Hide sidebar') ?>"><i class="fa fa-chevron-left"></i></a></div>
    <div class="sidebar-expand" style="display: none"><a href="#" title="<?= t('Expand sidebar') ?>"><i class="fa fa-chevron-right"></i></a></div>
</div>
