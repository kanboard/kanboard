<section id="main">

    <?= Helper\template('board/filters', array(
        'categories' => $categories,
        'users' => $users,
        'project' => $project,
    )) ?>

    <?php if (empty($board)): ?>
        <p class="alert alert-error"><?= t('There is no column in your project!') ?></p>
    <?php else: ?>
        <?= Helper\template('board/show', array(
                'project' => $project,
                'board' => $board,
                'categories' => $categories,
                'board_private_refresh_interval' => $board_private_refresh_interval,
                'board_highlight_period' => $board_highlight_period,
        )) ?>
    <?php endif ?>

</section>
