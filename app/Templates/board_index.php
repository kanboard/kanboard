<section id="main">

    <div class="page-header board">
        <h2>
            <?= t('Project "%s"', $current_project_name) ?>
        </h2>
        <ul>
            <?php foreach ($projects as $project_id => $project_name): ?>
            <?php if ($project_id != $current_project_id): ?>
            <li>
                <a href="?controller=board&amp;action=show&amp;project_id=<?= $project_id ?>"><?= Helper\escape($project_name) ?></a>
            </li>
            <?php endif ?>
            <?php endforeach ?>
        </ul>
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
            <li><a href="?controller=project&amp;action=search&amp;project_id=<?= $current_project_id ?>"><?= t('Search') ?></a></li>
            <li><a href="?controller=project&amp;action=tasks&amp;project_id=<?= $current_project_id ?>"><?= t('Completed tasks') ?></a></li>
        </ul>
    </div>

    <?php if (empty($board)): ?>
        <p class="alert alert-error"><?= t('There is no column in your project!') ?></p>
    <?php else: ?>
        <?= Helper\template('board_show', array('current_project_id' => $current_project_id, 'board' => $board, 'categories' => $categories)) ?>
    <?php endif ?>

</section>

<script type="text/javascript" src="assets/js/board.js"></script>
