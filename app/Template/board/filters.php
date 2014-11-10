<div class="page-header">
    <ul>
        <li>
            <?= t('Filter by user') ?>
            <?= Helper\form_select('user_id', $users) ?>
        </li>
        <li>
            <?= t('Filter by category') ?>
            <?= Helper\form_select('category_id', $categories) ?>
        </li>
        <li>
            <a href="#" id="filter-due-date"><?= t('Filter by due date') ?></a>
        </li>
        <li>
            <i class="fa fa-search"></i>
            <?= Helper\a(t('Search'), 'project', 'search', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <i class="fa fa-check-square-o fa-fw"></i>
            <?= Helper\a(t('Completed tasks'), 'project', 'tasks', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <i class="fa fa-dashboard fa-fw"></i>
            <?= Helper\a(t('Activity'), 'project', 'activity', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <i class="fa fa-line-chart fa-fw"></i>
            <?= Helper\a(t('Analytics'), 'analytic', 'repartition', array('project_id' => $project['id'])) ?>
        </li>
        <?php if (Helper\is_admin()): ?>
            <li><i class="fa fa-cog fa-fw"></i>
            <?= Helper\a(t('Configure'), 'project', 'show', array('project_id' => $project['id'])) ?>
        <?php endif ?>
    </ul>
</div>