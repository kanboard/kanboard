<div class="page-header">
    <ul class="board-filters">
        <li>
            <ul class="dropdown">
                <li>
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
                            <i class="fa fa-search fa-fw"></i>
                            <?= $this->a(t('Search'), 'project', 'search', array('project_id' => $project['id'])) ?>
                        </li>
                        <li>
                            <i class="fa fa-check-square-o fa-fw"></i>
                            <?= $this->a(t('Completed tasks'), 'project', 'tasks', array('project_id' => $project['id'])) ?>
                        </li>
                        <li>
                            <i class="fa fa-dashboard fa-fw"></i>
                            <?= $this->a(t('Activity'), 'project', 'activity', array('project_id' => $project['id'])) ?>
                        </li>
                        <li>
                            <i class="fa fa-calendar fa-fw"></i>
                            <?= $this->a(t('Calendar'), 'calendar', 'show', array('project_id' => $project['id'])) ?>
                        </li>
                        <?php if ($project['is_public']): ?>
                        <li>
                            <i class="fa fa-share-alt fa-fw"></i> <?= $this->a(t('Public link'), 'board', 'readonly', array('token' => $project['token']), false, '', '', true) ?>
                        </li>
                        <?php endif ?>
                        <?php if ($this->acl->isManagerActionAllowed($project['id'])): ?>
                        <li>
                            <i class="fa fa-line-chart fa-fw"></i>
                            <?= $this->a(t('Analytics'), 'analytic', 'tasks', array('project_id' => $project['id'])) ?>
                        </li>
                        <li>
                            <i class="fa fa-cog fa-fw"></i>
                            <?= $this->a(t('Configure'), 'project', 'show', array('project_id' => $project['id'])) ?>
                        </li>
                        <?php endif ?>
                    </ul>
                </li>
            </ul>
        </li>
        <li>
            <?= $this->formSelect('user_id', $users, array(), array(), array('data-placeholder="'.t('Filter by user').'"'), 'apply-filters chosen-select') ?>
        </li>
        <li>
            <?= $this->formSelect('category_id', $categories, array(), array(), array('data-placeholder="'.t('Filter by category').'"'), 'apply-filters chosen-select') ?>
        </li>
        <li>
            <select id="more-filters" multiple data-placeholder="<?= t('More filters') ?>" class="apply-filters chosen-select hide-mobile">
                <option value=""></option>
                <option value="filter-due-date"><?= t('Filter by due date') ?></option>
                <option value="filter-recent"><?= t('Filter recently updated') ?></option>
            </select>
        </li>
    </ul>
</div>