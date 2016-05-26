<div class="dropdown">
    <a href="#" class="dropdown-menu action-menu"><?= t('Menu') ?> <i class="fa fa-caret-down"></i></a>
    <ul>
        <?php if ($board_view): ?>
        <li>
            <span class="filter-display-mode" <?= $this->board->isCollapsed($project['id']) ? '' : 'style="display: none;"' ?>>
                <i class="fa fa-expand fa-fw"></i>
                <?= $this->url->link(t('Expand tasks'), 'board', 'expand', array('project_id' => $project['id']), false, 'board-display-mode', t('Keyboard shortcut: "%s"', 's')) ?>
            </span>
            <span class="filter-display-mode" <?= $this->board->isCollapsed($project['id']) ? 'style="display: none;"' : '' ?>>
                <i class="fa fa-compress fa-fw"></i>
                <?= $this->url->link(t('Collapse tasks'), 'board', 'collapse', array('project_id' => $project['id']), false, 'board-display-mode', t('Keyboard shortcut: "%s"', 's')) ?>
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
        <li>
            <span class="filter-max-height" style="display: none">
                <i class="fa fa-arrows-v fa-fw"></i> <a href="#" class="filter-toggle-height"><?= t('Set maximum column height') ?></a>
            </span>
            <span class="filter-min-height">
                <i class="fa fa-arrows-v fa-fw"></i> <a href="#" class="filter-toggle-height"><?= t('Remove maximum column height') ?></a>
            </span>
        </li>
        <?php endif ?>

        <?php if ($this->user->hasProjectAccess('TaskCreationController', 'show', $project['id'])): ?>
            <li>
                <i class="fa fa-plus fa-fw"></i>
                <?= $this->url->link(t('Add a new task'), 'TaskCreationController', 'show', array('project_id' => $project['id']), false, 'popover') ?>
            </li>
        <?php endif ?>

        <li>
            <i class="fa fa-dashboard fa-fw"></i>
            <?= $this->url->link(t('Activity'), 'activity', 'project', array('project_id' => $project['id'])) ?>
        </li>

        <?php if ($this->user->hasProjectAccess('customfilter', 'index', $project['id'])): ?>
            <li>
                <i class="fa fa-filter fa-fw"></i>
                <?= $this->url->link(t('Custom filters'), 'customfilter', 'index', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>

        <?php if ($project['is_public']): ?>
            <li>
                <i class="fa fa-share-alt fa-fw"></i>
                <?= $this->url->link(t('Public link'), 'board', 'readonly', array('token' => $project['token']), false, '', '', true) ?>
            </li>
        <?php endif ?>

        <?= $this->hook->render('template:project:dropdown', array('project' => $project)) ?>

        <?php if ($this->user->hasProjectAccess('analytic', 'tasks', $project['id'])): ?>
            <li>
                <i class="fa fa-line-chart fa-fw"></i>
                <?= $this->url->link(t('Analytics'), 'analytic', 'tasks', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>

        <?php if ($this->user->hasProjectAccess('export', 'tasks', $project['id'])): ?>
            <li>
                <i class="fa fa-download fa-fw"></i>
                <?= $this->url->link(t('Exports'), 'export', 'tasks', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>

        <?php if ($this->user->hasProjectAccess('ProjectEdit', 'edit', $project['id'])): ?>
            <li>
                <i class="fa fa-cog fa-fw"></i>
                <?= $this->url->link(t('Settings'), 'ProjectViewController', 'show', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>

        <li>
            <i class="fa fa-folder fa-fw" aria-hidden="true"></i>
            <?= $this->url->link(t('Manage projects'), 'ProjectListController', 'show') ?>
        </li>
    </ul>
</div>
