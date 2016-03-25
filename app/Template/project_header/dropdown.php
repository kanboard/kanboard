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
        <?= $this->render('project/dropdown', array('project' => $project)) ?>
    </ul>
</div>