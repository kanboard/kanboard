<section id="main">
    <div class="page-header">
        <ul>
            <li>
                <?= t('Filter by user') ?>
                <?= Helper\form_select('user_id', $users, $filters) ?>
            </li>
            <li>
                <?= t('Filter by category') ?>
                <?= Helper\form_select('category_id', $categories, $filters) ?>
            </li>
            <li>
                <a href="#" id="filter-due-date"><?= t('Filter by due date') ?></a>
            </li>
            <li>
                <i class="fa fa-search"></i>
                <?= Helper\a(t('Search'), 'project', 'search', array('project_id' => $current_project_id)) ?>
            </li>
            <li>
                <i class="fa fa-check-square-o fa-fw"></i>
                <?= Helper\a(t('Completed tasks'), 'project', 'tasks', array('project_id' => $current_project_id)) ?>
            </li>
            <li>
                <i class="fa fa-dashboard fa-fw"></i>
                <?= Helper\a(t('Activity'), 'project', 'activity', array('project_id' => $current_project_id)) ?>
            </li>
            <?php if (Helper\is_admin()): ?>
                <li><i class="fa fa-cog fa-fw"></i>
                <?= Helper\a(t('Configure'), 'project', 'show', array('project_id' => $current_project_id)) ?>
            <?php endif ?>
        </ul>
    </div>

    <?php if (empty($board)): ?>
        <p class="alert alert-error"><?= t('There is no column in your project!') ?></p>
    <?php else: ?>
        <?= Helper\template('board_show', array(
                'current_project_id' => $current_project_id,
                'board' => $board,
                'categories' => $categories,
                'board_private_refresh_interval' => $board_private_refresh_interval,
                'board_highlight_period' => $board_highlight_period,
        )) ?>
    <?php endif ?>

</section>
