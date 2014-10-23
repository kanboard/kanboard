<section id="main">

    <div class="page-header board">
        <h2>
            <?php if(Helper\is_admin()): ?>
            <a href="?controller=project&amp;action=show&amp;project_id=<?= $current_project_id ?>"><i class="fa fa-cog"></i></a>
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
            <li><a href="#" id="filter-due-date"><?= t('Filter by due date') ?></a></li>
            <li><i class="fa fa-search"></i> <a href="?controller=project&amp;action=search&amp;project_id=<?= $current_project_id ?>"><?= t('Search') ?></a></li>
            <li><i class="fa fa-check-square-o"></i> <a href="?controller=project&amp;action=tasks&amp;project_id=<?= $current_project_id ?>"><?= t('Completed tasks') ?></a></li>
            <li><i class="fa fa-dashboard"></i> <a href="?controller=project&amp;action=activity&amp;project_id=<?= $current_project_id ?>"><?= t('Activity') ?></a></li>
            <?php if(Helper\is_admin()): ?>
            <li><i class="fa fa-cog"></i> <a href="?controller=board&amp;action=edit&amp;project_id=<?= $current_project_id ?>"><?= t('Edit board') ?></a></li>
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
