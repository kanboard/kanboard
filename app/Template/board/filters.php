<div class="page-header">
    <ul class="board-filters">
        <li class="hide-tablet">
            <?= t('Filter by user') ?>
            <?= $this->formSelect('user_id', $users) ?>
        </li>
        <li class="hide-tablet">
            <?= t('Filter by category') ?>
            <?= $this->formSelect('category_id', $categories) ?>
        </li>
        <li class="hide-tablet">
            <a href="#" id="filter-due-date"><?= t('Filter by due date') ?></a>
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
</div>