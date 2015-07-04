<div class="page-header">
    <div class="dropdown">
        <span>
            <i class="fa fa-caret-down"></i> <a href="#" class="dropdown-menu"><?= t('Actions') ?></a>
            <ul>
                <?php if (isset($is_board)): ?>
                <li>
                    <span class="filter-collapse">
                        <i class="fa fa-compress fa-fw"></i> <a href="#" class="filter-collapse-link"><?= t('Collapse tasks') ?></a>
                    </span>
                    <span class="filter-expand" style="display: none">
                        <i class="fa fa-expand fa-fw"></i> <a href="#" class="filter-expand-link"><?= t('Expand tasks') ?></a>
                    </span>
                </li>
                <li>
                    <span class="filter-compact">
                        <i class="fa fa-th fa-fw"></i> <a href="#" class="filter-toggle-scrolling"><?= t('Compact view') ?></a>
                    </span>
                    <span class="filter-wide" style="display: none">
                        <i class="fa fa-arrows-h fa-fw"></i> <a href="#" class="filter-toggle-scrolling"><?= t('Horizontal scrolling') ?></a>
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
            <?= $this->url->link(t('Board'), 'board', 'show', array('project_id' => $project['id'], 'search' => $filters['search'])) ?>
        </li>
        <li <?= $filters['controller'] === 'calendar' ? 'class="active"' : '' ?>>
            <i class="fa fa-calendar fa-fw"></i>
            <?= $this->url->link(t('Calendar'), 'calendar', 'show', array('project_id' => $project['id'], 'search' => $filters['search'])) ?>
        </li>
        <li <?= $filters['controller'] === 'listing' ? 'class="active"' : '' ?>>
            <i class="fa fa-list fa-fw"></i>
            <?= $this->url->link(t('List'), 'listing', 'show', array('project_id' => $project['id'], 'search' => $filters['search'])) ?>
        </li>
    </ul>
    <form method="get" action="?" class="search">
        <?= $this->form->hidden('project_id', $filters) ?>
        <?= $this->form->hidden('controller', $filters) ?>
        <?= $this->form->hidden('action', $filters) ?>
        <?= $this->form->text('search', $filters, array(), array('placeholder="'.t('Filter').'"'), 'form-input-large') ?>
    </form>
    <?= $this->render('app/filters_helper', array('reset' => 'status:open')) ?>
</div>