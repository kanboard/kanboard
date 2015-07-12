<div class="page-header">
    <div class="dropdown">
        <span>
            <i class="fa fa-caret-down"></i> <a href="#" class="dropdown-menu"><?= t('Actions') ?></a>
            <ul>
                <?php if (isset($is_board)): ?>
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
                <?php endif ?>
                <?= $this->render('project/dropdown', array('project' => $project)) ?>
            </ul>
        </span>
    </div>
    <ul class="views">
        <li <?= $filters['controller'] === 'board' ? 'class="active"' : '' ?>>
            <i class="fa fa-th fa-fw"></i>
            <?= $this->url->link(t('Board'), 'board', 'show', array('project_id' => $project['id'], 'search' => $filters['search']), false, 'view-board', t('Keyboard shortcut: "%s"', 'v b')) ?>
        </li>
        <li <?= $filters['controller'] === 'calendar' ? 'class="active"' : '' ?>>
            <i class="fa fa-calendar fa-fw"></i>
            <?= $this->url->link(t('Calendar'), 'calendar', 'show', array('project_id' => $project['id'], 'search' => $filters['search']), false, 'view-calendar', t('Keyboard shortcut: "%s"', 'v c')) ?>
        </li>
        <li <?= $filters['controller'] === 'listing' ? 'class="active"' : '' ?>>
            <i class="fa fa-list fa-fw"></i>
            <?= $this->url->link(t('List'), 'listing', 'show', array('project_id' => $project['id'], 'search' => $filters['search']), false, 'view-listing', t('Keyboard shortcut: "%s"', 'v l')) ?>
        </li>
    </ul>
    <form method="get" action="<?= $this->url->dir() ?>" class="search">
        <?= $this->form->hidden('controller', $filters) ?>
        <?= $this->form->hidden('action', $filters) ?>
        <?= $this->form->hidden('project_id', $filters) ?>
        <?= $this->form->text('search', $filters, array(), array('placeholder="'.t('Filter').'"'), 'form-input-large') ?>
    </form>
    <?= $this->render('app/filters_helper', array('reset' => 'status:open')) ?>
</div>