<section id="main">

    <div class="page-header board">
        <h2>
            <?php if (Helper\is_admin()): ?>
                <?= Helper\a('<i class="fa fa-cog"></i>', 'project', 'show', array('project_id' => $current_project_id)) ?>
            <?php endif ?>
            <?= t('Project "%s"', $current_project_name) ?>
        </h2>
    </div>

    <div class="project-menu">
        <ul>
            <li>
                <span class="hide-tablet"><?= t('Filter by user') ?></span>
                <?= Helper\form_select('user_id', $users, $filters) ?>
            </li>
            <li>
                <span class="hide-tablet"><?= t('Filter by category') ?></span>
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
                <i class="fa fa-check-square-o"></i>
                <?= Helper\a(t('Completed tasks'), 'project', 'tasks', array('project_id' => $current_project_id)) ?>
            </li>
            <li>
                <i class="fa fa-dashboard"></i>
                <?= Helper\a(t('Activity'), 'project', 'activity', array('project_id' => $current_project_id)) ?>
            </li>
            <?php if (Helper\is_admin()): ?>
                <li><i class="fa fa-cog"></i>
                <?= Helper\a(t('Edit board'), 'board', 'edit', array('project_id' => $current_project_id)) ?>
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
