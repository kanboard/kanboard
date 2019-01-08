<div class="dropdown">
    <a href="#" class="dropdown-menu action-menu" title="<?= t('Configure this project') ?>"><i class="fa fa-cog"></i><i class="fa fa-caret-down"></i></a>
    <ul>
        <?php if ($board_view): ?>
        <li>
            <span class="filter-display-mode" <?= $this->board->isCollapsed($project['id']) ? '' : 'style="display: none;"' ?>>
                <?= $this->url->icon('expand', t('Expand tasks'), 'BoardAjaxController', 'expand', array('project_id' => $project['id']), false, 'board-display-mode', t('Keyboard shortcut: "%s"', 's')) ?>
            </span>
            <span class="filter-display-mode" <?= $this->board->isCollapsed($project['id']) ? 'style="display: none;"' : '' ?>>
                <?= $this->url->icon('compress', t('Collapse tasks'), 'BoardAjaxController', 'collapse', array('project_id' => $project['id']), false, 'board-display-mode', t('Keyboard shortcut: "%s"', 's')) ?>
            </span>
        </li>
        <li>
            <span class="filter-compact">
                <i class="fa fa-th fa-fw"></i> <a href="#" class="filter-toggle-scrolling" title="<?= t('Keyboard shortcut: "%s"', 'c') ?>"><?= t('Compact view') ?></a>
            </span>
            <span class="filter-wide" style="display: none">
                <i class="fa fa-arrows-h fa-fw"></i> <a href="#" class="filter-toggle-scrolling" title="<?= t('Keyboard shortcut: "%s"', 'c') ?>"><?= t('Horizontal scrolling') ?></a>
            </span>
        </li>
        <?php endif ?>

        <?php if ($this->user->hasProjectAccess('TaskCreationController', 'show', $project['id'])): ?>
            <li>
                <?= $this->modal->large('plus', t('Add a new task'), 'TaskCreationController', 'show', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>

        <li>
            <?= $this->modal->medium('dashboard', t('Activity'), 'ActivityController', 'project', array('project_id' => $project['id'])) ?>
        </li>

        <?php if ($this->user->hasProjectAccess('CustomFilterController', 'index', $project['id'])): ?>
            <li>
                <?= $this->modal->medium('filter', t('Add custom filters'), 'CustomFilterController', 'create', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>

        <?php if ($project['is_public']): ?>
            <li>
                <?= $this->url->icon('share-alt', t('Public link'), 'BoardViewController', 'readonly', array('token' => $project['token']), false, '', '', true) ?>
            </li>
        <?php endif ?>

        <?= $this->hook->render('template:project:dropdown', array('project' => $project)) ?>

        <?php if ($this->user->hasProjectAccess('AnalyticController', 'taskDistribution', $project['id'])): ?>
            <li>
                <?= $this->modal->large('line-chart', t('Analytics'), 'AnalyticController', 'taskDistribution', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>

        <?php if ($this->user->hasProjectAccess('ExportController', 'tasks', $project['id'])): ?>
            <li>
                <?= $this->modal->medium('upload', t('Exports'), 'ExportController', 'tasks', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>

        <?php if ($this->user->hasProjectAccess('TaskImportController', 'tasks', $project['id'])): ?>
            <li>
                <?= $this->modal->medium('download', t('Import tasks'), 'TaskImportController', 'show', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>

        <?php if ($this->user->hasProjectAccess('ProjectViewController', 'show', $project['id'])): ?>
            <li>
                <?= $this->url->icon('cog', t('Configure this project'), 'ProjectViewController', 'show', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>

        <li>
            <?= $this->url->icon('folder', t('Manage projects'), 'ProjectListController', 'show') ?>
        </li>
    </ul>
</div>
