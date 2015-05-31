<div class="page-header">
    <ul class="board-filters">
        <li>
            <span class="dropdown">
                <span>
                    <i class="fa fa-caret-down"></i> <a href="#" class="dropdown-menu"><?= t('Actions') ?></a>
                    <ul>
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
                        <?= $this->render('project/dropdown', array('project' => $project)) ?>
                    </ul>
                </span>
            </span>
        </li>
        <li>
            <?= $this->form->select('user_id', $users, array(), array(), array('data-placeholder="'.t('Filter by user').'"', 'data-notfound="'.t('No results match:').'"'), 'apply-filters chosen-select') ?>
        </li>
        <li>
            <?= $this->form->select('category_id', $categories, array(), array(), array('data-placeholder="'.t('Filter by category').'"', 'data-notfound="'.t('No results match:').'"'), 'apply-filters chosen-select') ?>
        </li>
        <li>
            <select id="more-filters" multiple data-placeholder="<?= t('More filters') ?>" data-notfound="<?= t('No results match:') ?>" class="apply-filters hide-mobile">
                <option value=""></option>
                <option value="filter-due-date"><?= t('Filter by due date') ?></option>
                <option value="filter-recent"><?= t('Filter recently updated') ?></option>
            </select>
        </li>
    </ul>
</div>