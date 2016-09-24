<div class="dropdown">
    <a href="#" class="dropdown-menu action-menu"><?= t('Menu') ?> <i class="fa fa-caret-down"></i></a>
    <ul>
        <?php if ($board_view): ?>
        <li>
            <span class="filter-display-mode" <?= $this->board->isCollapsed($project['id']) ? '' : 'style="display: none;"' ?>>
                <?= $this->url->link('<i class="fa fa-expand fa-fw"></i>' . t('Expand tasks'), 'BoardAjaxController', 'expand', array('project_id' => $project['id']), false, 'board-display-mode', t('Keyboard shortcut: "%s"', 's')) ?>
            </span>
            <span class="filter-display-mode" <?= $this->board->isCollapsed($project['id']) ? 'style="display: none;"' : '' ?>>
                <?= $this->url->link('<i class="fa fa-compress fa-fw"></i>' . t('Collapse tasks'), 'BoardAjaxController', 'collapse', array('project_id' => $project['id']), false, 'board-display-mode', t('Keyboard shortcut: "%s"', 's')) ?>
            </span>
        </li>
        <li>
            <span class="filter-compact">
                <a href="#" class="filter-toggle-scrolling" title="<?= t('Keyboard shortcut: "%s"', 'c') ?>"><i class="fa fa-th fa-fw"></i><?= t('Compact view') ?></a>
            </span>
            <span class="filter-wide" style="display: none">
                <a href="#" class="filter-toggle-scrolling" title="<?= t('Keyboard shortcut: "%s"', 'c') ?>"><i class="fa fa-arrows-h fa-fw"></i><?= t('Horizontal scrolling') ?></a>
            </span>
        </li>
        <?php endif ?>

        <?php if ($this->user->hasProjectAccess('TaskCreationController', 'show', $project['id'])): ?>
            <li>
                <?= $this->url->link('<i class="fa fa-plus fa-fw"></i>' . t('Add a new task'), 'TaskCreationController', 'show', array('project_id' => $project['id']), false, 'popover') ?>
            </li>
        <?php endif ?>

        <li>
            <?= $this->url->link('<i class="fa fa-dashboard fa-fw"></i>' . t('Activity'), 'ActivityController', 'project', array('project_id' => $project['id'])) ?>
        </li>

        <?php if ($this->user->hasProjectAccess('CustomFilterController', 'index', $project['id'])): ?>
            <li>
                <?= $this->url->link('<i class="fa fa-filter fa-fw"></i>' . t('Custom filters'), 'CustomFilterController', 'index', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>

        <?php if ($project['is_public']): ?>
            <li>
                <?= $this->url->link('<i class="fa fa-share-alt fa-fw"></i>' . t('Public link'), 'BoardViewController', 'readonly', array('token' => $project['token']), false, '', '', true) ?>
            </li>
        <?php endif ?>

        <?= $this->hook->render('template:project:dropdown', array('project' => $project)) ?>

        <?php if ($this->user->hasProjectAccess('AnalyticController', 'taskDistribution', $project['id'])): ?>
            <li>
                <?= $this->url->link('<i class="fa fa-line-chart fa-fw"></i>' . t('Analytics'), 'AnalyticController', 'taskDistribution', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>

        <?php if ($this->user->hasProjectAccess('ExportController', 'tasks', $project['id'])): ?>
            <li>
                <?= $this->url->link('<i class="fa fa-upload fa-fw"></i>' . t('Exports'), 'ExportController', 'tasks', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>

        <?php if ($this->user->hasProjectAccess('TaskImportController', 'tasks', $project['id'])): ?>
            <li>
                <?= $this->url->link('<i class="fa fa-download fa-fw"></i>' . t('Imports'), 'TaskImportController', 'show', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>

        <?php if ($this->user->hasProjectAccess('ProjectEditController', 'edit', $project['id'])): ?>
            <li>
                <?= $this->url->link('<i class="fa fa-cog fa-fw"></i>' . t('Settings'), 'ProjectViewController', 'show', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>

        <li>
            <?= $this->url->link('<i class="fa fa-folder fa-fw" aria-hidden="true"></i>' . t('Manage projects'), 'ProjectListController', 'show') ?>
        </li>
    </ul>
</div>
